<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectReportSubmittedNotification extends Notification
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
            'title' => 'Project Report Submitted',
            'message' => 'A new project work report has been submitted and is awaiting your review and approval.',
            'url' => route('projects.show', $this->project->id),
        ];
    }
}
