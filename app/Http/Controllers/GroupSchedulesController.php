<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupScheduleRequest;
use App\Http\Requests\UpdateGroupScheduleRequest;
use App\Models\Group;
use App\Models\GroupSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GroupSchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group): View
    {
        $group->load('schedules');

        return view('schedules.index', ['group' => $group]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupScheduleRequest $request, Group $group): RedirectResponse
    {
        $group->schedules()->create($request->validated());

        return redirect()->route('groups.schedules.index', $group)->with('status', __('schedules.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, GroupSchedule $schedule): View
    {
        return view('schedules.edit', ['group' => $group, 'schedule' => $schedule]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupScheduleRequest $request, Group $group, GroupSchedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        return redirect()->route('groups.schedules.index', $group)->with('status', __('schedules.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, GroupSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('groups.schedules.index', $group)->with('status', __('schedules.deleted'));
    }
}
