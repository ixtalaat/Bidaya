<?php

namespace App\Actions\Students;

use App\Models\ParentStudent;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateStudentAction
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(Student $student, array $validated): Student
    {
        return DB::transaction(function () use ($student, $validated): Student {
            $photo = $validated['photo'] ?? null;
            $syncParents = array_key_exists('parents', $validated);
            $parentLinks = $validated['parents'] ?? [];
            unset($validated['photo'], $validated['parents']);

            $student->update($validated);

            if ($photo instanceof UploadedFile) {
                if ($student->photo_path !== null) {
                    Storage::disk('public')->delete($student->photo_path);
                }

                $student->photo_path = $photo->store('student-photos', 'public');
                $student->save();
            }

            if ($syncParents) {
                $student->guardians()->sync(ParentStudent::syncData($parentLinks));
            }

            return $student->refresh();
        });
    }
}
