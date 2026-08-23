<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use App\Enums\LessonParticipationEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'first_name',
    'last_name',
    'email',
    'password',
    'role',
    'karma',
    'team_id',
    'last_payment',
    'profile_photo_path',
])]
#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token'
])]
#[Appends([
    'profile_photo_url',
    'name',
    'nickname',
    'open_payment',
])]
class User extends Authenticatable implements PasskeyUser, OAuthenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, HasPushSubscriptions;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'role' => UserRoleEnum::class,
            'last_payment' => 'datetime',
            'settings' => AsArrayObject::class,
        ];
    }

    public function name(): Attribute
    {
        return Attribute::get(function(): string {
            return $this->first_name . " " . $this->last_name;
        });
    }

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

    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function(): string {
            return $this->profile_photo_path ? asset($this->profile_photo_path) : $this->defaultProfilePhotoUrl();
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

    public function compensationLessons(): BelongsToMany
    {
        $courses = Course::where('id', 'in', function (Builder $query) {
            $query->select('compensation_id')
                ->from('compensations')
                ->whereColumn('original_id', '=', 'courses.id');
        })->get();
        dd($courses);
        return $courses;
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
        return false;
    }

    public function openPayment(): Attribute
    {
        return Attribute::get(function (): int {
            $teaching_payment = DB::table('lesson_user')
                ->join('users', 'lesson_user.user_id', '=', 'users.id')
                ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                ->join('courses', 'lessons.course_id', '=', 'courses.id')
                ->where('users.id', $this->id)
                ->whereRaw('finish BETWEEN last_payment AND NOW()')
                ->where('participation', '=', LessonParticipationEnum::TEACHER)
                ->sum('teacher_payment');

            $assist_payment = DB::table('lesson_user')
                ->join('users', 'lesson_user.user_id', '=', 'users.id')
                ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                ->join('courses', 'lessons.course_id', '=', 'courses.id')
                ->where('users.id', $this->id)
                ->whereRaw('finish BETWEEN last_payment AND NOW()')
                ->where('participation', '=', LessonParticipationEnum::ASSISTANCE)
                ->sum('assist_payment');

            return $teaching_payment + $assist_payment;
        });
    }

    protected function defaultProfilePhotoUrl()
    {
        $name = $this->first_name . $this->last_name;
        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }
}
