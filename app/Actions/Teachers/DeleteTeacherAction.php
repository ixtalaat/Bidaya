<?php

namespace App\Actions\Teachers;

use App\Enums\GroupStatus;
use App\Models\Teacher;
use Illuminate\Validation\ValidationException;

class DeleteTeacherAction
{
    /**
     * @throws ValidationException
     */
    public function handle(Teacher $teacher): void
    {
        if ($teacher->groups()->where('status', GroupStatus::Active)->exists()) {
            throw ValidationException::withMessages([
                'teacher' => __('teachers.has_active_groups'),
            ]);
        }

        $teacher->user->delete();
    }
}
