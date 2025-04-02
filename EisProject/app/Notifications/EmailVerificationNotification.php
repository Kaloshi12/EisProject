<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $url;
    protected $password;
    public function __construct($url,$password)
    {
        $this->url = $url;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verification Eamil')
            ->line("Dear ".$notifiable->name)
            ->line('Your verification code is here :'. $notifiable->verification_code)
            ->line('Your temporary password is here :'. $this->password)
            ->line('Please click the button below to verify your email address and to set a new password.')
            ->line('Thank you to signup with our application.')
            ->action('Verify Email', $this->url)
            ->greeting('Thank you for using our application!');
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
