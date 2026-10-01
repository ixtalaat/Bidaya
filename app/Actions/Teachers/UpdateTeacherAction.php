<?php

namespace App\Actions\Teachers;

use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class UpdateTeacherAction
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(Teacher $teacher, array $validated): Teacher
    {
        return DB::transaction(function () use ($teacher, $validated): Teacher {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = $validated['password'];
            }

            $teacher->user->update($userData);

            $teacher->update([
                'phone' => $validated['phone'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
                'hire_date' => $validated['hire_date'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return $teacher->refresh();
        });
    }
}
