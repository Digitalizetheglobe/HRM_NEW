<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceRegularisation;
use App\Models\User;
use App\Models\Employee;
use App\Notifications\AttendanceRegularisationNotification;

class SendPendingRegularisationNotifications extends Command
{
    protected $signature = 'regularisation:send-notifications';
    protected $description = 'Send notifications for pending attendance regularisation requests';

    public function handle()
    {
        // Get all pending regularisation requests
        $pendingRequests = AttendanceRegularisation::where('status', 'Pending')
            ->with('employee')
            ->get();

        $this->info("Found {$pendingRequests->count()} pending requests");

        foreach ($pendingRequests as $regularisation) {
            $employee = $regularisation->employee;
            
            if (!$employee) {
                $this->warn("Skipping regularisation ID {$regularisation->id} - no employee found");
                continue;
            }

            // Get company users
            $companyUsers = User::where('type', 'company')
                ->where('created_by', $regularisation->created_by)
                ->get();

            // Get HR and Director users
            $hrDirectorUsers = User::where(function($query) {
                    $query->where('type', 'hr')
                          ->orWhere('type', 'HR')
                          ->orWhere('type', 'director')
                          ->orWhere('type', 'Director');
                })
                ->where('created_by', $regularisation->created_by)
                ->get();

            $notificationData = [
                'regularisation_id' => $regularisation->id,
                'message' => 'New attendance regularisation request: ' . $employee->name . ' submitted a request for ' . \Carbon\Carbon::parse($regularisation->missed_attendance_date)->format('M d, Y'),
                'employee_name' => $employee->name,
                'date' => $regularisation->missed_attendance_date,
                'url' => route('attendance-regularisation.index'),
            ];

            $sentCount = 0;

            // Send to company users
            foreach ($companyUsers as $user) {
                // Check if notification already exists
                $existingNotification = $user->notifications()
                    ->where('type', 'App\Notifications\AttendanceRegularisationNotification')
                    ->whereJsonContains('data->regularisation_id', $regularisation->id)
                    ->first();

                if (!$existingNotification) {
                    $user->notify(new AttendanceRegularisationNotification($notificationData));
                    $sentCount++;
                    $this->info("Sent notification to company user: {$user->email}");
                } else {
                    $this->line("Notification already exists for user: {$user->email}");
                }
            }

            // Send to HR/Director users
            foreach ($hrDirectorUsers as $user) {
                $existingNotification = $user->notifications()
                    ->where('type', 'App\Notifications\AttendanceRegularisationNotification')
                    ->whereJsonContains('data->regularisation_id', $regularisation->id)
                    ->first();

                if (!$existingNotification) {
                    $user->notify(new AttendanceRegularisationNotification($notificationData));
                    $sentCount++;
                    $this->info("Sent notification to HR/Director user: {$user->email}");
                } else {
                    $this->line("Notification already exists for user: {$user->email}");
                }
            }

            $this->info("Regularisation ID {$regularisation->id}: Sent {$sentCount} new notifications");
        }

        $this->info("Done!");
        return 0;
    }
}










