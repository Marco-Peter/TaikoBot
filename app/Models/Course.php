<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
    ];

    protected $attributes = [
        'name' => '',
        'description' => '',
        'capacity' => 0,
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

    public function firstLesson(): HasOne
    {
        return $this->hasOne(Lesson::class)->oldest('start');
    }

    public function lastLesson(): HasOne
    {
        return $this->hasOne(Lesson::class)->latest('finish');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('paid')->withTimestamps();
    }

    public function participants_paid(): BelongsToMany
    {
        return $this->participants()->wherePivot('paid', 1)->withTimestamps();
    }

    public function material(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }
}
