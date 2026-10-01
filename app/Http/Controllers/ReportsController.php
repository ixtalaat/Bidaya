<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\AssessmentResult;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Session;
use App\Services\FinanceMetrics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function __construct(private FinanceMetrics $finance) {}

    public function financial(Request $request): View
    {
        $from = $request->string('from')->toString() ?: today()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: today()->toDateString();

        $payments = (new Payment)->getTable();
        $invoices = (new Invoice)->getTable();
        $enrollments = (new Enrollment)->getTable();
        $groups = (new Group)->getTable();
        $courses = (new Course)->getTable();

        $base = Payment::toBase()
            ->where("{$payments}.status", PaymentStatus::Completed->value)
            ->whereDate("{$payments}.payment_date", '>=', $from)
            ->whereDate("{$payments}.payment_date", '<=', $to);

        $byCourse = (clone $base)
            ->join($invoices, "{$invoices}.id", '=', "{$payments}.invoice_id")
            ->join($enrollments, "{$enrollments}.id", '=', "{$invoices}.enrollment_id")
            ->join($groups, "{$groups}.id", '=', "{$enrollments}.group_id")
            ->join($courses, "{$courses}.id", '=', "{$groups}.course_id")
            ->selectRaw("{$courses}.name as course, sum({$payments}.amount) as collected, count({$payments}.id) as payments_count")
            ->groupBy("{$courses}.id", "{$courses}.name")
            ->orderByDesc('collected')
            ->get();

        $daily = (clone $base)
            ->selectRaw("date({$payments}.payment_date) as day, sum({$payments}.amount) as collected")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('reports.financial', [
            'from' => $from,
            'to' => $to,
            'collected' => round((float) (clone $base)->sum("{$payments}.amount"), 2),
            'outstanding' => $this->finance->outstandingTotal(),
            'byCourse' => $byCourse,
            'daily' => $daily,
        ]);
    }

    public function attendance(Request $request): View
    {
        $groupId = $request->integer('group_id');
        $from = $request->string('from')->toString() ?: today()->subDays(30)->toDateString();
        $to = $request->string('to')->toString() ?: today()->toDateString();

        $rows = [];

        if ($groupId) {
            $group = Group::findOrFail($groupId);

            $enrollments = $group->enrollments()
                ->with('student')
                ->withCount([
                    'attendances as total_count' => fn ($query) => $query->whereHas(
                        'session',
                        fn ($sessions) => $sessions->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)
                    ),
                    'attendances as attended_count' => fn ($query) => $query
                        ->whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])
                        ->whereHas(
                            'session',
                            fn ($sessions) => $sessions->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)
                        ),
                ])
                ->orderBy('enrollments.id')
                ->get();

            $rows = $enrollments->map(fn (Enrollment $enrollment): array => [
                'student' => $enrollment->student->name,
                'total' => $enrollment->total_count,
                'attended' => $enrollment->attended_count,
                'rate' => $enrollment->total_count > 0
                    ? round($enrollment->attended_count / $enrollment->total_count * 100, 1)
                    : null,
            ])->all();
        }

        return view('reports.attendance', [
            'allGroups' => Group::with('course')->orderBy('name')->get(),
            'groupId' => $groupId,
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
        ]);
    }

    public function progress(Request $request): View
    {
        $groupId = $request->integer('group_id');

        $rows = [];

        if ($groupId) {
            $group = Group::findOrFail($groupId);

            $enrollments = $group->enrollments()->with(['student', 'invoices'])->orderBy('enrollments.id')->get();
            $enrollmentIds = $enrollments->pluck('id')->all();

            $attendanceMap = Attendance::toBase()
                ->whereIn('enrollment_id', $enrollmentIds)
                ->selectRaw('enrollment_id, count(*) as total')
                ->selectRaw('sum(case when status in (?, ?) then 1 else 0 end) as attended', [
                    AttendanceStatus::Present->value,
                    AttendanceStatus::Late->value,
                ])
                ->groupBy('enrollment_id')
                ->get()
                ->keyBy('enrollment_id');

            $gradeMap = AssessmentResult::toBase()
                ->whereIn('enrollment_id', $enrollmentIds)
                ->selectRaw('enrollment_id, round(avg(score), 2) as average_score, count(*) as grades_count')
                ->groupBy('enrollment_id')
                ->get()
                ->keyBy('enrollment_id');

            $paidMap = Payment::toBase()
                ->join(
                    (new Invoice)->getTable(),
                    (new Invoice)->getTable().'.id',
                    '=',
                    (new Payment)->getTable().'.invoice_id'
                )
                ->whereIn((new Invoice)->getTable().'.enrollment_id', $enrollmentIds)
                ->where((new Payment)->getTable().'.status', PaymentStatus::Completed->value)
                ->selectRaw((new Invoice)->getTable().'.enrollment_id as enrollment_id, sum('.(new Payment)->getTable().'.amount) as paid')
                ->groupBy((new Invoice)->getTable().'.enrollment_id')
                ->get()
                ->keyBy('enrollment_id');

            $rows = $enrollments->map(function (Enrollment $enrollment) use ($attendanceMap, $gradeMap, $paidMap): array {
                $attendance = $attendanceMap->get($enrollment->id);
                $total = (int) ($attendance->total ?? 0);

                $invoiced = $enrollment->invoices
                    ->reject(fn (Invoice $invoice): bool => $invoice->status === InvoiceStatus::Cancelled)
                    ->sum(fn (Invoice $invoice): float => (float) $invoice->total);
                $paid = (float) ($paidMap->get($enrollment->id)->paid ?? 0);

                return [
                    'student' => $enrollment->student->name,
                    'status' => $enrollment->status->label(),
                    'attendanceRate' => $total > 0 ? round((int) $attendance->attended / $total * 100, 1) : null,
                    'averageScore' => $gradeMap->get($enrollment->id)->average_score ?? null,
                    'gradesCount' => (int) ($gradeMap->get($enrollment->id)->grades_count ?? 0),
                    'outstanding' => max(0, round($invoiced - $paid, 2)),
                ];
            })->all();
        }

        return view('reports.progress', [
            'allGroups' => Group::with('course')->orderBy('name')->get(),
            'groupId' => $groupId,
            'rows' => $rows,
        ]);
    }

    public function courses(): View
    {
        $payments = (new Payment)->getTable();
        $invoices = (new Invoice)->getTable();
        $enrollments = (new Enrollment)->getTable();
        $groups = (new Group)->getTable();

        $courses = Course::withCount('groups')->orderBy('name')->get();
        $courseIds = $courses->pluck('id')->all();

        $enrolledMap = Enrollment::toBase()
            ->join($groups, "{$groups}.id", '=', "{$enrollments}.group_id")
            ->whereIn("{$groups}.course_id", $courseIds)
            ->whereIn("{$enrollments}.status", [EnrollmentStatus::Active->value, EnrollmentStatus::Suspended->value, EnrollmentStatus::Pending->value])
            ->selectRaw("{$groups}.course_id as course_id, count({$enrollments}.id) as enrolled")
            ->groupBy("{$groups}.course_id")
            ->get()
            ->keyBy('course_id');

        $sessions = (new Session)->getTable();
        $attendances = (new Attendance)->getTable();

        $attendanceMap = Attendance::toBase()
            ->join($sessions, "{$sessions}.id", '=', "{$attendances}.session_id")
            ->join($groups, "{$groups}.id", '=', "{$sessions}.group_id")
            ->whereIn("{$groups}.course_id", $courseIds)
            ->selectRaw("{$groups}.course_id as course_id, count({$attendances}.id) as total")
            ->selectRaw("sum(case when {$attendances}.status in (?, ?) then 1 else 0 end) as attended", [
                AttendanceStatus::Present->value,
                AttendanceStatus::Late->value,
            ])
            ->groupBy("{$groups}.course_id")
            ->get()
            ->keyBy('course_id');

        $revenueMap = Payment::toBase()
            ->join($invoices, "{$invoices}.id", '=', "{$payments}.invoice_id")
            ->join($enrollments, "{$enrollments}.id", '=', "{$invoices}.enrollment_id")
            ->join($groups, "{$groups}.id", '=', "{$enrollments}.group_id")
            ->where("{$payments}.status", PaymentStatus::Completed->value)
            ->whereIn("{$groups}.course_id", $courseIds)
            ->selectRaw("{$groups}.course_id as course_id, sum({$payments}.amount) as revenue")
            ->groupBy("{$groups}.course_id")
            ->get()
            ->keyBy('course_id');

        $rows = $courses->map(function (Course $course) use ($enrolledMap, $attendanceMap, $revenueMap): array {
            $attendance = $attendanceMap->get($course->id);
            $total = (int) ($attendance->total ?? 0);

            return [
                'course' => $course->name,
                'groups' => $course->groups_count,
                'enrolled' => (int) ($enrolledMap->get($course->id)->enrolled ?? 0),
                'attendanceRate' => $total > 0 ? round((int) $attendance->attended / $total * 100, 1) : null,
                'revenue' => round((float) ($revenueMap->get($course->id)->revenue ?? 0), 2),
            ];
        })->all();

        return view('reports.courses', ['rows' => $rows]);
    }
}
