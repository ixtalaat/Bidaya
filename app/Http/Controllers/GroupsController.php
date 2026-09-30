<?php

namespace App\Http\Controllers;

use App\Enums\GroupStatus;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = GroupStatus::tryFrom($request->string('status')->toString());

        $groups = Group::query()
            ->when(
                $request->string('search')->toString(),
                fn (Builder $query, string $term) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('classroom', 'like', "%{$term}%")
            )
            ->when(
                $request->integer('course_id'),
                fn (Builder $query, int $courseId) => $query->where('course_id', $courseId)
            )
            ->when(
                $status,
                fn (Builder $query, GroupStatus $status) => $query->where('status', $status)
            )
            ->with(['course', 'teacher.user'])
            ->withCount('activeEnrollments')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('groups.index', [
            'groups' => $groups,
            'allCourses' => Course::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('groups.create', [
            'allCourses' => Course::orderBy('name')->get(),
            'allTeachers' => Teacher::with('user')->orderBy('id')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request): RedirectResponse
    {
        $group = Group::create($request->validated());

        return redirect()->route('groups.show', $group)->with('status', __('groups.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group): View
    {
        $group->load(['course', 'teacher.user', 'enrollments.student']);

        return view('groups.show', ['group' => $group]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group): View
    {
        return view('groups.edit', [
            'group' => $group,
            'allCourses' => Course::orderBy('name')->get(),
            'allTeachers' => Teacher::with('user')->orderBy('id')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group): RedirectResponse
    {
        $group->update($request->validated());

        return redirect()->route('groups.show', $group)->with('status', __('groups.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group): RedirectResponse
    {
        if ($group->enrollments()->active()->exists()) {
            return back()->withErrors(['group' => __('groups.has_active_enrollments')]);
        }

        $group->delete();

        return redirect()->route('groups.index')->with('status', __('groups.deleted'));
    }
}
