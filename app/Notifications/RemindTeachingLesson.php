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

class RemindTeachingLesson extends Notification
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
        $url = url(route('lessons.edit', $this->lesson));
        $ds = Carbon::parse($this->lesson->start)->format('l, j. M y, G:i');

        return (new MailMessage)
            ->subject("Upcoming Teaching Lesson")
            ->greeting("Hello $notifiable->first_name!")
            ->line('This is just a quick reminder, that you will be teaching soon.')
            ->line("The lesson starts at: $ds")
            ->action('View and edit lesson', $url)
            ->line('Thank you for your participation!');
    }

    public function toWebPush(object $notifiable): WebPushMessage
    {
        $url = url(route('lessons.edit', $this->lesson));
        $ds = Carbon::parse($this->lesson->start)->format('l, j. M y, G:i');

        return (new WebPushMessage)
        /* No actions for now...
        ->action([])
        */
        ->badge(URL::to('/images/favicon_black_64.png'))
        ->title("You will be teaching soon!")
        ->body("Your next teaching lesson will be at: $ds")
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
