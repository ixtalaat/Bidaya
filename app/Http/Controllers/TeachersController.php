<?php

namespace App\Http\Controllers;

use App\Actions\Teachers\CreateTeacherAction;
use App\Actions\Teachers\DeleteTeacherAction;
use App\Actions\Teachers\UpdateTeacherAction;
use App\Enums\TeacherStatus;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeachersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = TeacherStatus::tryFrom($request->string('status')->toString());

        $teachers = Teacher::query()
            ->when(
                $request->string('search')->toString(),
                fn (Builder $query, string $term) => $query
                    ->where('specialization', 'like', "%{$term}%")
                    ->orWhereHas('user', fn (Builder $users) => $users
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%"))
            )
            ->when(
                $status,
                fn (Builder $query, TeacherStatus $status) => $query->where('status', $status)
            )
            ->with('user')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('teachers.index', ['teachers' => $teachers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request, CreateTeacherAction $create): RedirectResponse
    {
        $teacher = $create->handle($request->validated());

        return redirect()->route('teachers.show', $teacher)->with('status', __('teachers.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher): View
    {
        $teacher->load(['user', 'groups.course']);

        return view('teachers.show', ['teacher' => $teacher]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('teachers.edit', ['teacher' => $teacher]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher, UpdateTeacherAction $update): RedirectResponse
    {
        $teacher = $update->handle($teacher, $request->validated());

        return redirect()->route('teachers.show', $teacher)->with('status', __('teachers.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher, DeleteTeacherAction $delete): RedirectResponse
    {
        $delete->handle($teacher);

        return redirect()->route('teachers.index')->with('status', __('teachers.deleted'));
    }
}
