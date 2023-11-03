<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
        return $this->belongsToMany(Team::class)->withTimestamps();
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

    public function invitees(): Builder
    {
        return User::select([
            'users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.email_verified_at',
            'users.password', 'users.two_factor_secret', 'users.two_factor_recovery_codes',
            'users.two_factor_confirmed_at', 'users.role', 'users.karma', 'users.team_id', 'users.income_group_id',
            'users.remember_token', 'users.profile_photo_path', 'users.created_at', 'users.updated_at'
        ])
            ->join('course_team', 'users.team_id', '=', 'course_team.team_id')
            ->join('courses', 'courses.id', '=', 'course_team.course_id')
            ->where('courses.id', $this->id);
    }
}
