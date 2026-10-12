<?php

namespace App\Actions\Sessions;

use App\Enums\SessionStatus;
use App\Models\Session;
use Illuminate\Validation\ValidationException;

class DeleteSessionAction
{
    /**
     * @throws ValidationException
     */
    public function handle(Session $session): void
    {
        if ($session->status === SessionStatus::Completed) {
            throw ValidationException::withMessages([
                'session' => __('sessions.completed_locked'),
            ]);
        }

        $session->delete();
    }
}
