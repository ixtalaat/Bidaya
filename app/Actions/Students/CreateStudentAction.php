<?php

namespace App\Actions\Students;

use App\Models\ParentStudent;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateStudentAction
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(array $validated): Student
    {
        return DB::transaction(function () use ($validated): Student {
            $photo = $validated['photo'] ?? null;
            $parentLinks = $validated['parents'] ?? [];
            unset($validated['photo'], $validated['parents']);

            $student = Student::create($validated);

            if ($photo instanceof UploadedFile) {
                $student->photo_path = $photo->store('student-photos', 'public');
                $student->save();
            }

            $student->guardians()->sync(ParentStudent::syncData($parentLinks));

            return $student;
        });
    }
}
