<?php

namespace App\Actions\Courses;

use App\Models\Course;
use Illuminate\Validation\ValidationException;

class DeleteCourseAction
{
    /**
     * @throws ValidationException
     */
    public function handle(Course $course): void
    {
        if ($course->groups()->exists()) {
            throw ValidationException::withMessages([
                'course' => __('courses.has_groups'),
            ]);
        }

        $course->delete();
    }
}
