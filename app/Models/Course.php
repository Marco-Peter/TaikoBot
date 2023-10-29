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
        return $this->belongsToMany(Team::class);
    }

    /* Contains courses, which can be used for compensation */
    public function compensation(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'compensations', 'original_id', 'compensation_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function fees(): BelongsToMany
    {
        return $this->belongsToMany(IncomeGroup::class, 'fees')->withPivot('fee');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('paid');
    }
}
