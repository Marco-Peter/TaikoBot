<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\LessonParticipationEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'role' => UserRoleEnum::class,
            'settings' => AsArrayObject::class,
            'password' => 'hashed',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Return all lessons
     *
     * Returns all lessons independent of the participation state.
     */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->withPivot('participation')
            ->withTimestamps()->using(LessonUser::class);
    }

    /**
     * Return all lessons as a student
     *
     * Returns all lessons where a user is a student (signed in or signed out doesn't matter).
     */
    public function studentLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->withPivot('participation')
            ->withTimestamps()->wherePivot('participation', '<>', LessonParticipationEnum::TEACHER->value);
    }

    /**
     * Return all lessons as a teacher
     *
     * Returns all lessons where a user is a teacher.
     */
    public function teacherLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->withPivot('participation')
            ->withTimestamps()->wherePivot('participation', '=', LessonParticipationEnum::TEACHER->value);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot('paid')->withTimestamps();
    }

    public function hasSignedUpToCourse(Course $course): bool
    {
        return $this->courses()->where('id', $course->id)->exists();
    }

    public function hasSignedInToLesson(Lesson $lesson): bool
    {
        return $this->lessons()->where('id', $lesson->id)->exists();
    }

    public function accepts_mail_notifications(): bool
    {
        dd($this->settings);
        return false;
    }
}
