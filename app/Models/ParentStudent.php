<?php

namespace App\Models;

use App\Enums\RelationshipType;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ParentStudent extends Pivot
{
    protected $table = 'parent_student';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'relationship' => RelationshipType::class,
        ];
    }

    /**
     * Build sync data for the guardian-student pivot.
     *
     * @param  array<int, array{id: int, relationship: string}>  $links
     * @return array<int, array{relationship: string}>
     */
    public static function syncData(array $links): array
    {
        $sync = [];

        foreach ($links as $link) {
            $sync[$link['id']] = ['relationship' => $link['relationship']];
        }

        return $sync;
    }
}
