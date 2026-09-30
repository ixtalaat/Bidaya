<?php

namespace App\Http\Controllers;

use App\Actions\Students\CreateStudentAction;
use App\Actions\Students\UpdateStudentAction;
use App\Enums\StudentStatus;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = StudentStatus::tryFrom($request->string('status')->toString());

        $students = Student::query()
            ->when(
                $request->string('search')->toString(),
                fn (Builder $query, string $term) => $query->search($term)
            )
            ->when(
                $status,
                fn (Builder $query, StudentStatus $status) => $query->withStatus($status)
            )
            ->when(
                $request->integer('parent_id'),
                fn (Builder $query, int $parentId) => $query->whereHas(
                    'guardians',
                    fn (Builder $guardians) => $guardians->where('parents.id', $parentId)
                )
            )
            ->with('guardians')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('students.index', ['students' => $students]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('students.create', [
            'allGuardians' => Guardian::orderBy('name')->orderBy('id')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request, CreateStudentAction $create): RedirectResponse
    {
        $student = $create->handle($request->validated());

        return redirect()->route('students.show', $student)->with('status', __('students.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): View
    {
        $student->load('guardians');

        return view('students.show', ['student' => $student]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student): View
    {
        $student->load('guardians');

        return view('students.edit', [
            'student' => $student,
            'allGuardians' => Guardian::orderBy('name')->orderBy('id')->get(),
            'linkedParents' => $student->guardians
                ->map(fn (Guardian $guardian): array => [
                    'id' => $guardian->id,
                    'relationship' => $guardian->pivot->relationship->value,
                ])
                ->all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student, UpdateStudentAction $update): RedirectResponse
    {
        $student = $update->handle($student, $request->validated());

        return redirect()->route('students.show', $student)->with('status', __('students.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        if ($student->photo_path !== null) {
            Storage::disk('public')->delete($student->photo_path);
        }

        $student->delete();

        return redirect()->route('students.index')->with('status', __('students.deleted'));
    }
}
