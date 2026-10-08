<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\CourseStatus;
use App\Enums\GroupStatus;
use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Enums\StudentStatus;
use App\Enums\TeacherStatus;
use App\Models\AssessmentResult;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Session;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\FinanceMetrics;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private FinanceMetrics $finance) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            Role::SuperAdmin, Role::Manager => $this->admin(),
            Role::Teacher => $this->teacher($user),
            Role::Parent => $this->parent($user),
            Role::Accountant => $this->accountant(),
            Role::Student => $this->student($user),
        };
    }

    private function admin(): View
    {
        $today = today()->toDateString();

        return view('dashboard.admin', [
            'activeStudents' => Student::withStatus(StudentStatus::Active)->count(),
            'activeCourses' => Course::where('status', CourseStatus::Active)->count(),
            'activeGroups' => Group::where('status', GroupStatus::Active)->count(),
            'activeTeachers' => Teacher::where('status', TeacherStatus::Active)->count(),
            'todaySessions' => Session::whereDate('date', $today)
                ->with(['group.course', 'teacher.user'])
                ->orderBy('start_time')
                ->orderBy('id')
                ->get(),
            'attendanceToday' => $this->attendanceRateForSessions(
                Session::whereDate('date', $today)->pluck('id')->all()
            ),
            'outstanding' => $this->outstandingTotal(),
            'recentEnrollments' => Enrollment::with(['student', 'group.course'])
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
            'recentPayments' => Payment::with(['invoice.enrollment.student'])
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ]);
    }

    private function teacher(User $user): View
    {
        $teacher = $user->teacher;

        if ($teacher === null) {
            return view('dashboard.unlinked');
        }

        $today = today()->toDateString();

        return view('dashboard.teacher', [
            'todaySessions' => Session::where('teacher_id', $teacher->id)
                ->whereDate('date', $today)
                ->with(['group.course', 'attendances'])
                ->orderBy('start_time')
                ->get(),
            'upcomingSessions' => Session::where('teacher_id', $teacher->id)
                ->whereDate('date', '>', $today)
                ->whereDate('date', '<=', today()->addDays(7)->toDateString())
                ->with('group.course')
                ->orderBy('date')
                ->orderBy('start_time')
                ->limit(10)
                ->get(),
            'groups' => $teacher->groups()
                ->with('course')
                ->withCount('activeEnrollments')
                ->orderBy('name')
                ->get(),
        ]);
    }

    private function parent(User $user): View
    {
        $guardian = $user->guardian;

        if ($guardian === null) {
            return view('dashboard.unlinked');
        }

        $students = $guardian->students()->orderBy('name')->orderBy('id')->get();

        return view('dashboard.parent', [
            'children' => $students->map(fn (Student $student): array => $this->childOverview($student))->all(),
            'unreadNotifications' => $user->unreadNotifications()->limit(5)->get(),
        ]);
    }

    private function student(User $user): View
    {
        $student = $user->student;

        if ($student === null) {
            return view('dashboard.unlinked');
        }

        return view('dashboard.parent', [
            'children' => [$this->childOverview($student->fresh())],
            'unreadNotifications' => $user->unreadNotifications()->limit(5)->get(),
        ]);
    }

    private function accountant(): View
    {
        $today = today()->toDateString();

        return view('dashboard.accountant', [
            'collectedToday' => $this->collectedBetween($today, $today),
            'collectedMonth' => $this->collectedBetween(today()->startOfMonth()->toDateString(), today()->endOfMonth()->toDateString()),
            'outstanding' => $this->outstandingTotal(),
            'pendingInvoices' => Invoice::whereIn('status', [InvoiceStatus::Pending, InvoiceStatus::PartiallyPaid])->count(),
            'recentPayments' => Payment::with(['invoice.enrollment.student', 'receiver'])
                ->orderByDesc('id')
                ->limit(8)
                ->get(),
        ]);
    }

    /**
     * @return array{student: Student, enrollments: Collection<int, Enrollment>, attendanceRate: ?float, outstanding: float, upcomingSessions: Collection<int, Session>, recentGrades: Collection<int, AssessmentResult>}
     */
    private function childOverview(Student $student): array
    {
        $enrollments = $student->enrollments()
            ->active()
            ->with(['group.course', 'group.teacher.user', 'invoices'])
            ->get();

        $enrollmentIds = $enrollments->pluck('id')->all();

        $outstanding = $enrollments
            ->flatMap(fn (Enrollment $enrollment) => $enrollment->invoices)
            ->reject(fn (Invoice $invoice): bool => $invoice->status === InvoiceStatus::Cancelled)
            ->sum(fn (Invoice $invoice): float => $invoice->remaining());

        $groupIds = $enrollments->pluck('group_id')->all();

        return [
            'student' => $student,
            'enrollments' => $enrollments,
            'attendanceRate' => $this->attendanceRateForEnrollments($enrollmentIds)['rate'],
            'outstanding' => round($outstanding, 2),
            'upcomingSessions' => Session::whereIn('group_id', $groupIds)
                ->whereDate('date', '>=', today()->toDateString())
                ->whereDate('date', '<=', today()->addDays(7)->toDateString())
                ->with('group.course')
                ->orderBy('date')
                ->orderBy('start_time')
                ->limit(10)
                ->get(),
            'recentGrades' => AssessmentResult::whereIn('enrollment_id', $enrollmentIds)
                ->with(['assessment.group.course'])
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * @param  array<int, int>  $sessionIds
     * @return array{attended: int, total: int, rate: ?float}
     */
    private function attendanceRateForSessions(array $sessionIds): array
    {
        if ($sessionIds === []) {
            return ['attended' => 0, 'total' => 0, 'rate' => null];
        }

        $row = Attendance::toBase()
            ->whereIn('session_id', $sessionIds)
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when status in (?, ?) then 1 else 0 end) as attended', AttendanceStatus::attendedValues())
            ->first();

        return $this->rateFromRow($row);
    }

    /**
     * @param  array<int, int>  $enrollmentIds
     * @return array{attended: int, total: int, rate: ?float}
     */
    private function attendanceRateForEnrollments(array $enrollmentIds): array
    {
        if ($enrollmentIds === []) {
            return ['attended' => 0, 'total' => 0, 'rate' => null];
        }

        $row = Attendance::toBase()
            ->whereIn('enrollment_id', $enrollmentIds)
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when status in (?, ?) then 1 else 0 end) as attended', AttendanceStatus::attendedValues())
            ->first();

        return $this->rateFromRow($row);
    }

    /**
     * @return array{attended: int, total: int, rate: ?float}
     */
    private function rateFromRow(?object $row): array
    {
        $total = (int) ($row->total ?? 0);
        $attended = (int) ($row->attended ?? 0);

        return [
            'attended' => $attended,
            'total' => $total,
            'rate' => $total > 0 ? round($attended / $total * 100, 1) : null,
        ];
    }

    private function outstandingTotal(): float
    {
        return $this->finance->outstandingTotal();
    }

    private function collectedBetween(string $from, string $to): float
    {
        return $this->finance->collectedBetween($from, $to);
    }
}
