<?php

namespace App\Http\Controllers;

use App\Actions\Sessions\GenerateGroupSessionsAction;
use App\Actions\Sessions\UpdateSessionStatusAction;
use App\Enums\GroupStatus;
use App\Enums\Role;
use App\Enums\SessionStatus;
use App\Http\Requests\GenerateSessionsRequest;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Http\Requests\UpdateSessionStatusRequest;
use App\Models\Group;
use App\Models\Session;
use App\Services\SessionConflictService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SessionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = SessionStatus::tryFrom($request->string('status')->toString());

        $sessions = Session::query()
            ->when(
                $request->user()->hasRole(Role::Teacher),
                fn (Builder $query) => $query->where('teacher_id', $request->user()->teacher?->id)
            )
            ->when(
                $request->integer('group_id'),
                fn (Builder $query, int $groupId) => $query->where('group_id', $groupId)
            )
            ->when(
                $request->string('date')->toString(),
                fn (Builder $query, string $date) => $query->whereDate('date', $date)
            )
            ->when(
                $status,
                fn (Builder $query, SessionStatus $status) => $query->where('status', $status)
            )
            ->with(['group.course', 'teacher.user'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('sessions.index', [
            'sessions' => $sessions,
            'allGroups' => Group::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('sessions.create', [
            'allGroups' => Group::with('course')->whereIn('status', [GroupStatus::Draft, GroupStatus::Active])->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request, SessionConflictService $conflicts): RedirectResponse
    {
        $group = Group::findOrFail($request->integer('group_id'));
        $classroom = $request->input('classroom') ?? $group->classroom;

        $found = $conflicts->findConflicts(
            $request->string('date')->toString(),
            $request->string('start_time')->toString(),
            $request->string('end_time')->toString(),
            $group->teacher_id,
            $classroom
        );

        if ($found->isNotEmpty()) {
            return back()->withInput()
                ->withErrors(['start_time' => __('sessions.conflict_found')])
                ->with('conflicts', $found);
        }

        $session = Session::create([
            ...$request->validated(),
            'teacher_id' => $group->teacher_id,
            'classroom' => $classroom,
            'status' => SessionStatus::Scheduled,
        ]);

        return redirect()->route('sessions.show', $session)->with('status', __('sessions.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Session $session): View
    {
        Gate::authorize('view', $session);

        $session->load(['group.course', 'teacher.user']);

        return view('sessions.show', ['session' => $session]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Session $session): View
    {
        $session->load('group.course');

        return view('sessions.edit', ['session' => $session]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessionRequest $request, Session $session, SessionConflictService $conflicts): RedirectResponse
    {
        $classroom = $request->input('classroom') ?? $session->group->classroom;

        $found = $conflicts->findConflicts(
            $request->string('date')->toString(),
            $request->string('start_time')->toString(),
            $request->string('end_time')->toString(),
            $session->teacher_id,
            $classroom,
            $session->id
        );

        if ($found->isNotEmpty()) {
            return back()->withInput()
                ->withErrors(['start_time' => __('sessions.conflict_found')])
                ->with('conflicts', $found);
        }

        $session->update([...$request->validated(), 'classroom' => $classroom]);

        return redirect()->route('sessions.show', $session)->with('status', __('sessions.updated'));
    }

    /**
     * Change the session status following allowed transitions.
     */
    public function updateStatus(UpdateSessionStatusRequest $request, Session $session, UpdateSessionStatusAction $update): RedirectResponse
    {
        Gate::authorize('update', $session);

        $session = $update->handle($session, $request->validated());

        return redirect()->route('sessions.show', $session)->with('status', __('sessions.status_updated'));
    }

    /**
     * Show the session generation form with a preview.
     */
    public function generate(Group $group, GenerateSessionsRequest $request, GenerateGroupSessionsAction $generate): View
    {
        $preview = $generate->preview(
            $group,
            $request->string('start_date')->toString(),
            $request->string('end_date')->toString()
        );

        $group->load('schedules');

        return view('sessions.generate', [
            'group' => $group,
            'preview' => $preview,
            'startDate' => $request->string('start_date')->toString(),
            'endDate' => $request->string('end_date')->toString(),
        ]);
    }

    /**
     * Generate sessions from the group schedule.
     */
    public function storeGenerated(Group $group, GenerateSessionsRequest $request, GenerateGroupSessionsAction $generate): RedirectResponse
    {
        $result = $generate->handle(
            $group,
            $request->string('start_date')->toString(),
            $request->string('end_date')->toString()
        );

        return redirect()->route('sessions.index', ['group_id' => $group->id])
            ->with('status', __('sessions.generated', ['count' => $result['created']]))
            ->with('skipped', $result['skipped']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Session $session): RedirectResponse
    {
        if ($session->status === SessionStatus::Completed) {
            return back()->withErrors(['session' => __('sessions.completed_locked')]);
        }

        $session->delete();

        return redirect()->route('sessions.index')->with('status', __('sessions.deleted'));
    }
}
