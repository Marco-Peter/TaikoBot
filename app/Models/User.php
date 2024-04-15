<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\LessonParticipationEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
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
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => UserRoleEnum::class,
        'settings' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'nickname',
    ];

    public function nickname(): Attribute
    {
        return Attribute::get(function (): string {
            if (User::where('first_name', $this->first_name)->count() > 1) {
                if (
                    User::where('first_name', $this->first_name)
                    ->where('last_name', 'like', $this->last_name[0] . '%')->count() > 1
                ) {
                    return ucfirst($this->first_name . ucfirst($this->last_name));
                } else {
                    return ucfirst($this->first_name . strtoupper($this->last_name[0]));
                }
            } else {
                return ucfirst($this->first_name);
            }
        });
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
        return $this->loadExists(['courses' => function (Builder $query) use ($course) {
            $query->where('id', $course->id);
        }])->courses_exists;
    }

    public function hasSignedInToLesson(Lesson $lesson): bool
    {
        return $this->loadExists(['lessons' => function (Builder $query) use ($lesson) {
            $query->where('id', $lesson->id);
        }])->lessons_exists;
    }

    public function accepts_mail_notifications(): bool
    {
        dd($this->settings);
        return false;
    }
}
