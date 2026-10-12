<?php

namespace App\Actions\Groups;

use App\Models\Group;
use Illuminate\Validation\ValidationException;

class DeleteGroupAction
{
    /**
     * @throws ValidationException
     */
    public function handle(Group $group): void
    {
        if ($group->enrollments()->active()->exists()) {
            throw ValidationException::withMessages([
                'group' => __('groups.has_active_enrollments'),
            ]);
        }

        $group->delete();
    }
}
