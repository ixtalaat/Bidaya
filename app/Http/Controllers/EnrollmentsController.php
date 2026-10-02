<?php

namespace App\Http\Controllers;

use App\Actions\Enrollment\EnrollStudentAction;
use App\Actions\Enrollment\UpdateEnrollmentStatusAction;
use App\Enums\EnrollmentStatus;
use App\Enums\GroupStatus;
use App\Enums\StudentStatus;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = EnrollmentStatus::tryFrom($request->string('status')->toString());

        $enrollments = Enrollment::query()
            ->when(
                $request->string('search')->toString(),
                fn (Builder $query, string $term) => $query->whereHas(
                    'student',
                    fn (Builder $students) => $students->where('name', 'like', "%{$term}%")
                )
            )
            ->when(
                $request->integer('group_id'),
                fn (Builder $query, int $groupId) => $query->where('group_id', $groupId)
            )
            ->when(
                $status,
                fn (Builder $query, EnrollmentStatus $status) => $query->where('status', $status)
            )
            ->with(['student', 'group.course'])
            ->orderByDesc('enrollment_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('enrollments.index', [
            'enrollments' => $enrollments,
            'allGroups' => Group::with('course')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('enrollments.create', [
            'allStudents' => Student::withStatus(StudentStatus::Active)->orderBy('name')->orderBy('id')->get(),
            'allGroups' => Group::with('course')->where('status', GroupStatus::Active)->orderBy('name')->get(),
            'preselectedGroupId' => $request->integer('group_id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEnrollmentRequest $request, EnrollStudentAction $enroll): RedirectResponse
    {
        $enrollment = $enroll->handle($request->validated());

        return redirect()->route('enrollments.show', $enrollment)->with('status', __('enrollments.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'group.course', 'group.teacher.user', 'invoices', 'coveringEnrollment', 'certificate']);

        return view('enrollments.show', ['enrollment' => $enrollment]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'group.course']);

        return view('enrollments.edit', ['enrollment' => $enrollment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment, UpdateEnrollmentStatusAction $update): RedirectResponse
    {
        $enrollment = $update->handle($enrollment, $request->validated());

        return redirect()->route('enrollments.show', $enrollment)->with('status', __('enrollments.updated'));
    }
}
