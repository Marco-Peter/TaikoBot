<?php

/**
 * This module cancels pending waitlist entries depending on the
 * individual user settings.
 *
 * The idea is to start this module using the servers Cron demon.
 */

namespace App\Console\Commands;

use App\Enums\LessonParticipationEnum;
use App\Models\LessonUser;
use App\Notifications\WaitlistCanceled;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelWaitlist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-waitlist';

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
        Log::info("Canceling waitlist entries");
        $allEntries = LessonUser::where('participation', LessonParticipationEnum::WAITLIST)->get();
        foreach ($allEntries as $entry) {
            $startTime = $entry->lesson->start->inApplicationTz();
            $cancelHours = $entry->user->settings ?
                $entry->user->settings['waitlistCancelTime'] :
                env('APP_WAITLIST_AUTOCANCEL_TIME', '7');
            $cancelTime = $startTime->subHours($cancelHours);
            if ($cancelTime < Carbon::now()) {
                DB::table('lesson_user')
                    ->where('lesson_id', $entry->lesson->id)
                    ->where('user_id', $entry->user->id)
                    ->update([
                        'participation' => LessonParticipationEnum::SIGNED_OUT,
                        'message' => "Signed out from waitlist by TaikoBot",
                    ]);
                $entry->user->karma = $entry->user->karma + 1;
                $entry->user->save();
                $entry->user->notify(new WaitlistCanceled($entry->lesson));
            }
        }
    }
}
