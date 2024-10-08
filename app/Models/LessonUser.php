<?php

namespace App\Models;

use App\Enums\LessonParticipationEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonUser extends Pivot
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function setReminder()
    {
        $leadTime = $this->user->settings ? $this->user->settings['lessonNotificationTime'] : 0;
        $remind_at = $this->lesson->start->subHours($leadTime);

        if ($leadTime !== 0 && $remind_at > Carbon::now()) {
            $this->remind_at = $remind_at;
        } else {
            $this->remind_at = null;
        }
        $this->save();
    }

    protected $fillable = [
        'remind_at',
        'participation',
        'message',
    ];

    protected $casts = [
        'participation' => LessonParticipationEnum::class,
        'remind_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (LessonUser $lessonUser) {
            $leadTime = $lessonUser->user->settings ?
                $lessonUser->user->settings['lessonNotificationTime'] : 0;
            $remind_at = $lessonUser->lesson->start->subHours($leadTime);

            if ($leadTime !== 0 && $remind_at > Carbon::now()) {
                $lessonUser->remind_at = $remind_at;
            } else {
                $lessonUser->remind_at = null;
            }
        });
    }
}
