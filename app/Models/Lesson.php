<?php

namespace App\Models;

use App\Enums\LessonParticipationEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'start',
        'finish',
        'course_id',
        'title',
        'notes',
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('participation')
            ->withTimestamps()->using(LessonUser::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('participation')
            ->wherePivot('participation', '<>', LessonParticipationEnum::TEACHER->value);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('participation', LessonParticipationEnum::TEACHER->value)
            ->using(LessonUser::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    protected $casts = [
        'start' => 'datetime',
        'finish' => 'datetime',
    ];
}
