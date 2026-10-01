<?php

namespace App\Http\Controllers;

use App\Actions\Assessments\RecordAssessmentResultsAction;
use App\Http\Requests\StoreAssessmentResultsRequest;
use App\Models\Assessment;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AssessmentResultsController extends Controller
{
    public function edit(Group $group, Assessment $assessment): View
    {
        Gate::authorize('update', $assessment);

        $enrollments = $assessment->group->enrollments()
            ->active()
            ->with('student')
            ->orderBy('enrollments.id')
            ->get();

        $existing = $assessment->results()->get()->keyBy('enrollment_id');

        return view('assessments.results', [
            'group' => $group,
            'assessment' => $assessment,
            'enrollments' => $enrollments,
            'existing' => $existing,
        ]);
    }

    public function update(StoreAssessmentResultsRequest $request, Group $group, Assessment $assessment, RecordAssessmentResultsAction $record): RedirectResponse
    {
        Gate::authorize('update', $assessment);

        $count = $record->handle($assessment, $request->validated()['records']);

        return redirect()->route('groups.assessments.show', [$group, $assessment])->with('status', __('assessments.recorded', ['count' => $count]));
    }
}
