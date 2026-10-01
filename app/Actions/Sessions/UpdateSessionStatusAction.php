<?php

namespace App\Actions\Sessions;

use App\Enums\SessionStatus;
use App\Models\Session;
use Illuminate\Validation\ValidationException;

class UpdateSessionStatusAction
{
    /**
     * @param  array<string, mixed>  $validated
     *
     * @throws ValidationException
     */
    public function handle(Session $session, array $validated): Session
    {
        $newStatus = SessionStatus::from($validated['status']);

        if ($session->status !== $newStatus && ! in_array($newStatus, $session->status->allowedTransitions(), true)) {
            throw ValidationException::withMessages([
                'status' => __('sessions.invalid_transition'),
            ]);
        }

        $session->update([
            'status' => $newStatus,
            'teacher_notes' => $validated['teacher_notes'] ?? $session->teacher_notes,
        ]);

        return $session->refresh();
    }
}
