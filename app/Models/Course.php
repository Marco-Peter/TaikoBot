<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'fee',
        'capacity',
    ];

    /**
     * Return all teams to which this course is available
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot('signed_in');
    }

    public function teams_signed_in(): BelongsToMany {
        return $this->belongsToMany(Team::class)->wherePivot('signed_in', 1);
    }

    public function teams_not_signed_in(): BelongsToMany {
        return $this->belongsToMany(Team::class)->wherePivot('signed_in', 0);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
