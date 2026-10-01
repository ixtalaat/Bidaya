<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Models\Assessment;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AssessmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group): View
    {
        Gate::authorize('view', $group);

        $group->load('assessments');

        return view('assessments.index', ['group' => $group]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group): View
    {
        Gate::authorize('view', $group);
        Gate::authorize('create', Assessment::class);

        return view('assessments.create', ['group' => $group]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssessmentRequest $request, Group $group): RedirectResponse
    {
        Gate::authorize('view', $group);
        Gate::authorize('create', Assessment::class);

        $assessment = $group->assessments()->create($request->validated());

        return redirect()->route('groups.assessments.show', [$group, $assessment])->with('status', __('assessments.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group, Assessment $assessment): View
    {
        Gate::authorize('view', $assessment);

        $assessment->load(['group.course', 'results.enrollment.student']);

        return view('assessments.show', ['group' => $group, 'assessment' => $assessment]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, Assessment $assessment): View
    {
        Gate::authorize('update', $assessment);

        return view('assessments.edit', ['group' => $group, 'assessment' => $assessment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssessmentRequest $request, Group $group, Assessment $assessment): RedirectResponse
    {
        Gate::authorize('update', $assessment);

        $assessment->update($request->validated());

        return redirect()->route('groups.assessments.show', [$group, $assessment])->with('status', __('assessments.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, Assessment $assessment): RedirectResponse
    {
        Gate::authorize('delete', $assessment);

        $assessment->delete();

        return redirect()->route('groups.assessments.index', $group)->with('status', __('assessments.deleted'));
    }
}
