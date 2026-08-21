<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectReportApprovalNotification extends Notification
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
            'title' => 'Project Report Approved',
            'message' => 'Your project work report has been reviewed and approved successfully.',
            'url' => route('projects.show', $this->project->id),
        ];
    }
}
