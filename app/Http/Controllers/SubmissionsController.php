<?php

namespace App\Http\Controllers;

use App\Actions\Assignments\RecordSubmissionAction;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SubmissionsController extends Controller
{
    public function store(StoreSubmissionRequest $request, Group $group, Assignment $assignment, RecordSubmissionAction $record): RedirectResponse
    {
        $enrollment = Enrollment::with('student')->findOrFail($request->integer('enrollment_id'));

        if ($enrollment->group_id !== $group->id) {
            abort(404);
        }

        Gate::authorize('create', [Submission::class, $enrollment]);

        $record->handle($assignment, $enrollment, $request->validated(), $request->user());

        return redirect()->route('groups.assignments.show', [$group, $assignment])->with('status', __('submissions.submitted'));
    }

    public function update(UpdateSubmissionRequest $request, Group $group, Assignment $assignment, Submission $submission, RecordSubmissionAction $record): RedirectResponse
    {
        $submission->load(['enrollment.student', 'assignment']);

        Gate::authorize('update', $submission);

        $record->handle($assignment, $submission->enrollment, $request->validated(), $request->user());

        return redirect()->route('groups.assignments.show', [$group, $assignment])->with('status', __('submissions.updated'));
    }
}
