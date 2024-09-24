<?php

namespace App\Notifications;

use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class LessonConfirmed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('dashboard'));
        $ds = $this->lesson->start->inApplicationTz()->format('l, j. M y, G:i');

        return (new MailMessage)
            ->subject("Lesson Confirmed")
            ->greeting("Hello $notifiable->first_name!")
            ->line('Good news! You just got a space for your waitlisted lesson')
            ->line("The lesson starts at: $ds")
            ->action('Update my attendance', $url);
    }

    public function toWebPush(object $notifiable): WebPushMessage
    {
        $url = url(route('dashboard'));
        $ds = $this->lesson->start->inApplicationTz()->format('l, j. M y, G:i');

        return (new WebPushMessage)
            /* No actions for now...
        ->action([])
        */
            ->badge(URL::to('/images/favicon_black_64.png'))
            ->title("Lesson Confirmed!")
            ->body("Good news! You just got a space for your waitlisted lesson at: $ds")
            ->data([
                'url' => $url,
            ])
            ->icon(URL::to('/images/favicon_black_64.png'))
            ->options([
                'TTL' => 24 * 3600,
                'urgency' => 'normal',
                'batchSize' => 100,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
