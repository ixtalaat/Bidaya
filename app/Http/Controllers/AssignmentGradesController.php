<?php

namespace App\Http\Controllers;

use App\Actions\Assignments\RecordAssignmentGradesAction;
use App\Http\Requests\StoreAssignmentGradesRequest;
use App\Models\Assignment;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AssignmentGradesController extends Controller
{
    public function edit(Group $group, Assignment $assignment): View
    {
        Gate::authorize('update', $assignment);

        $enrollments = $assignment->group->enrollments()
            ->active()
            ->with('student')
            ->orderBy('enrollments.id')
            ->get();

        $existing = $assignment->submissions()->get()->keyBy('enrollment_id');

        return view('assignments.grades', [
            'group' => $group,
            'assignment' => $assignment,
            'enrollments' => $enrollments,
            'existing' => $existing,
        ]);
    }

    public function update(StoreAssignmentGradesRequest $request, Group $group, Assignment $assignment, RecordAssignmentGradesAction $record): RedirectResponse
    {
        Gate::authorize('update', $assignment);

        $count = $record->handle($assignment, $request->validated()['records']);

        return redirect()->route('groups.assignments.show', [$group, $assignment])->with('status', __('assignments.recorded', ['count' => $count]));
    }
}
