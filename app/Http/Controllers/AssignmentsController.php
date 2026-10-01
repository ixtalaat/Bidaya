<?php

namespace App\Http\Controllers;

use App\Enums\AssignmentStatus;
use App\Enums\Role;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AssignmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Group $group): View
    {
        Gate::authorize('view', $group);

        $query = $group->assignments()->orderBy('due_date')->orderBy('id');

        if (! $request->user()->hasRole(Role::SuperAdmin, Role::Manager)
            && $request->user()->teacher?->groups()->whereKey($group->id)->exists() !== true) {
            $query->where('status', AssignmentStatus::Published);
        }

        return view('assignments.index', ['group' => $group, 'assignments' => $query->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group): View
    {
        Gate::authorize('view', $group);
        Gate::authorize('create', Assignment::class);

        return view('assignments.create', ['group' => $group]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request, Group $group): RedirectResponse
    {
        Gate::authorize('view', $group);
        Gate::authorize('create', Assignment::class);

        $data = $request->validated();
        unset($data['attachment']);

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('assignment-attachments', 'public');
        }

        $assignment = $group->assignments()->create($data);

        return redirect()->route('groups.assignments.show', [$group, $assignment])->with('status', __('assignments.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Group $group, Assignment $assignment): View
    {
        Gate::authorize('view', $assignment);

        $assignment->load(['group.course', 'submissions.enrollment.student']);

        return view('assignments.show', [
            'group' => $group,
            'assignment' => $assignment,
            'submitTargets' => $this->submitTargets($request, $group, $assignment),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, Assignment $assignment): View
    {
        Gate::authorize('update', $assignment);

        return view('assignments.edit', ['group' => $group, 'assignment' => $assignment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentRequest $request, Group $group, Assignment $assignment): RedirectResponse
    {
        Gate::authorize('update', $assignment);

        $data = $request->validated();
        unset($data['attachment']);

        if ($request->hasFile('attachment')) {
            if ($assignment->attachment_path !== null) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }

            $data['attachment_path'] = $request->file('attachment')->store('assignment-attachments', 'public');
        }

        $assignment->update($data);

        return redirect()->route('groups.assignments.show', [$group, $assignment])->with('status', __('assignments.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, Assignment $assignment): RedirectResponse
    {
        Gate::authorize('delete', $assignment);

        if ($assignment->attachment_path !== null) {
            Storage::disk('public')->delete($assignment->attachment_path);
        }

        $assignment->submissions()->each(function ($submission): void {
            if ($submission->file_path !== null) {
                Storage::disk('public')->delete($submission->file_path);
            }
        });

        $assignment->delete();

        return redirect()->route('groups.assignments.index', $group)->with('status', __('assignments.deleted'));
    }

    /**
     * Enrollments the current user may submit homework for, with existing submissions.
     *
     * @return array<int, array{enrollment: Enrollment, submission: ?Submission}>
     */
    private function submitTargets(Request $request, Group $group, Assignment $assignment): array
    {
        $user = $request->user();
        $targets = [];

        $guardian = $user->guardian;

        if ($guardian !== null) {
            $enrollments = $guardian->students()->with(['enrollments' => fn ($query) => $query
                ->active()
                ->where('group_id', $group->id)
                ->with('student'),
            ])->get()->flatMap(fn ($student) => $student->enrollments)->values();
        } elseif ($user->student !== null) {
            $enrollments = $user->student->enrollments()->active()->where('group_id', $group->id)->with('student')->get();
        } else {
            return [];
        }

        $existing = $assignment->submissions()->whereIn('enrollment_id', $enrollments->pluck('id'))->get()->keyBy('enrollment_id');

        foreach ($enrollments as $enrollment) {
            $targets[] = ['enrollment' => $enrollment, 'submission' => $existing->get($enrollment->id)];
        }

        return $targets;
    }
}
