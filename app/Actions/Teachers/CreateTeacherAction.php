<?php

namespace App\Actions\Teachers;

use App\Enums\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTeacherAction
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(array $validated): Teacher
    {
        return DB::transaction(function () use ($validated): Teacher {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => Role::Teacher,
            ]);

            return $user->teacher()->create([
                'phone' => $validated['phone'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
                'hire_date' => $validated['hire_date'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });
    }
}
