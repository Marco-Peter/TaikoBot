<?php

/**
 * This module implements any user reminders which need to be applied.
 *
 * The idea is to start this module using the servers Cron demon.
 */

namespace App\Console\Commands;

use App\Enums\LessonParticipationEnum;
use App\Models\LessonUser;
use App\Notifications\RemindLesson;
use Illuminate\Console\Command;
use App\Notifications\RemindTeachingLesson;
use Illuminate\Support\Facades\Notification;

class RemindUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remind-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reminders = LessonUser::where('participation', LessonParticipationEnum::TEACHER)
            ->where('remind_at', '<', now(config('timezone')))->get();
        foreach ($reminders as $reminder) {
            $reminder->user->notify(new RemindTeachingLesson($reminder->lesson));
        }

        $reminders = LessonUser::where('participation', LessonParticipationEnum::SIGNED_IN)
            ->where('remind_at', '<', now(config('timezone')))->get();
        foreach ($reminders as $reminder) {
            $reminder->user->notify(new RemindLesson($reminder->lesson));
        }

        LessonUser::where('remind_at', '<', now(config('timezone')))
            ->update(['remind_at' => null,]);
    }
}
