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

    public static function booted()
    {
        static::created(function (User $user) {
            $user->messageChannels()->create([
                'name' => $user->first_name . "_" . $user->last_name,
            ]);
        });

        static::updated(function (User $user) {
            $ch = $user->messageChannels()
            ->where('name', $user->getOriginal('first_name') . "_" . $user->getOriginal('last_name'))->first();
            $ch->name = $user->first_name . "_" . $user->last_name;
            $ch->save();
        });

        static::deleting(function (User $user) {
            $user->messageChannels()
                ->where('name', $user->first_name . "_" . $user->last_name)
                ->delete();
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->withPivot('participation');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot('paid');
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
    public function messageChannels(): BelongsToMany
    {
        return $this->belongsToMany(MessageChannel::class);
    }
}
