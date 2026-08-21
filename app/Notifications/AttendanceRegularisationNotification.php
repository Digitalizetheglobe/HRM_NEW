<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceRegularisationNotification extends Notification
{
    use Queueable;

    public $notificationData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line($this->notificationData['message'])
                    ->action('View Request', $this->notificationData['url'])
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
            'regularisation_id' => $this->notificationData['regularisation_id'],
            'message' => $this->notificationData['message'],
            'employee_name' => $this->notificationData['employee_name'] ?? '',
            'date' => $this->notificationData['date'] ?? '',
            'url' => $this->notificationData['url'],
        ];
    }
}
