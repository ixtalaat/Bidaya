<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = CourseStatus::tryFrom($request->string('status')->toString());

        $courses = Course::query()
            ->when(
                $request->string('search')->toString(),
                fn (Builder $query, string $term) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%")
            )
            ->when(
                $status,
                fn (Builder $query, CourseStatus $status) => $query->where('status', $status)
            )
            ->withCount('groups')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('courses.index', ['courses' => $courses]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = Course::create($request->validated());

        return redirect()->route('courses.show', $course)->with('status', __('courses.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): View
    {
        $course->load(['groups' => fn ($query) => $query->orderBy('name')->orderBy('id')]);

        return view('courses.show', ['course' => $course]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): View
    {
        return view('courses.edit', ['course' => $course]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->route('courses.show', $course)->with('status', __('courses.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        if ($course->groups()->exists()) {
            return back()->withErrors(['course' => __('courses.has_groups')]);
        }

        $course->delete();

        return redirect()->route('courses.index')->with('status', __('courses.deleted'));
    }
}
