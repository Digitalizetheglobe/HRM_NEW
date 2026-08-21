<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectAssignmentNotification extends Notification
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
            'title' => 'New Project Assigned',
            'message' => 'A new project (' . $this->project->project_name . ') has been assigned to you. Please review the project details and start working on it.',
            'url' => route('projects.show', $this->project->id),
        ];
    }
}
