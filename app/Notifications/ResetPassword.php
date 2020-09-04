<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class ResetPassword extends Notification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(config('app.client_url') . '/password/reset/' . $this->token) . '?email=' . urlencode($notifiable->email);

        return (new MailMessage)
                    ->line('You are receiving this email because you requested to reset password of your account.')
                    ->action('Reset Password', $url)
                    ->line('Ignore if you did not request password reset!');
    }
}
