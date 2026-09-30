<?php

namespace App\Models;

use Database\Factories\GuardianFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guardian extends Model
{
    /** @use HasFactory<GuardianFactory> */
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'emergency_contact',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->using(ParentStudent::class)
            ->withPivot('relationship')
            ->withTimestamps();
    }
}
