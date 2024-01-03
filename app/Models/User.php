<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRoleEnum;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use PhpParser\Node\Expr\Cast\Bool_;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nickname',
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
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->withPivot('participation')->withTimestamps();
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

    /**
     * Return all message channels, the user has subscribed to.
     */
    public function subscribedMessageChannels(): BelongsToMany
    {
        return $this->belongsToMany(MessageChannel::class)->withPivot([
            'read_until',
            'can_post',
        ]);
    }

    /**
     * Return the own, dedicated message channel of the user.
     */
    public function messageChannel(): BelongsTo
    {
        return $this->belongsTo(MessageChannel::class);
    }
}
