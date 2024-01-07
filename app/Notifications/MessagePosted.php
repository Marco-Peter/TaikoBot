<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class MessagePosted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        //return ['mail'];
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
        /* No actions for now...
        ->action([])
        */
        ->badge(URL::to('/images/favicon_black_64.png'))
        ->body($this->message->content)
        ->data([
            'url' => route('channels.messages.index', ['channel' => $this->message->messageChannel->id]),
        ])
        ->icon(URL::to('/images/favicon_black_64.png'))
        ->options([
            'TTL' => 24 * 3600,
            'urgency' => 'normal',
            'batchSize' => 100,
        ])
        ->title("{$this->message->user->nickname} wrote in {$this->message->messageChannel->name}");
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
