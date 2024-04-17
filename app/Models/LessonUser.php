<?php

namespace App\Models;

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

    public function setReminder($leadTime)
    {
        $remind_time = Carbon::parse($this->lesson->start)->subHours($leadTime);

        if ($leadTime !== 0 && $remind_time > Carbon::now(config('timezone'))) {
            $this->remind_at = $remind_time;
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
}
