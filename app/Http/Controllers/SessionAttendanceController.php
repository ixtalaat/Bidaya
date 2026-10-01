<?php

namespace App\Http\Controllers;

use App\Actions\Attendance\RecordSessionAttendanceAction;
use App\Enums\Role;
use App\Http\Requests\StoreSessionAttendanceRequest;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SessionAttendanceController extends Controller
{
    public function edit(Session $session): View
    {
        Gate::authorize('update', $session);

        $session->load(['group.course', 'group.teacher.user']);

        $enrollments = $session->group->enrollments()
            ->active()
            ->with('student')
            ->orderBy('enrollments.id')
            ->get();

        $existing = $session->attendances()->pluck('status', 'enrollment_id');

        return view('attendance.edit', [
            'session' => $session,
            'enrollments' => $enrollments,
            'existing' => $existing,
        ]);
    }

    public function update(StoreSessionAttendanceRequest $request, Session $session, RecordSessionAttendanceAction $record): RedirectResponse
    {
        Gate::authorize('update', $session);

        $isStaff = $request->user()->hasRole(Role::SuperAdmin, Role::Manager);

        $count = $record->handle($session, $request->validated()['records'], $request->user()->id, $isStaff);

        return redirect()->route('sessions.show', $session)->with('status', __('attendance.recorded', ['count' => $count]));
    }
}
