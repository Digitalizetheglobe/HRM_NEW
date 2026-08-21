<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClientFeedbackNotification extends Notification
{
    use Queueable;

    protected $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Client Feedback Received',
            'message' => 'A client has submitted new feedback for a project. Please review the feedback and take the necessary design actions.',
            'url' => $this->project ? route('projects.show', $this->project->id) : '#',
        ];
    }
}
