<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceEmployee;
use Carbon\Carbon;

class AutoPunchOutMissed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-punch-out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically punch out employees who missed their punch out and set status to Half Day';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $missedAttendances = AttendanceEmployee::where('date', $today)
            ->where(function ($query) {
                $query->whereNull('clock_out')
                      ->orWhere('clock_out', '00:00:00');
            })
            ->whereNotNull('clock_in')
            ->where('clock_in', '!=', '00:00:00')
            ->get();

        $count = 0;
        foreach ($missedAttendances as $attendance) {
            $attendance->status = AttendanceEmployee::STATUS_HALF_DAY;
            $attendance->status_reason = 'Missed Punch-Out';
            $attendance->save();
            
            $count++;
        }

        $this->info("Successfully auto punched out {$count} employees for {$today}.");
        return Command::SUCCESS;
    }
}
