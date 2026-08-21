<?php

namespace App\Console\Commands;

use App\Models\AttendanceEmployee;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PurgeAttendanceMonth extends Command
{
    protected $signature = 'attendance:purge-month
                            {--month= : Month in YYYY-MM format (default: current month)}
                            {--emails=* : Employee emails to purge (default: config attendance.auto_present_emails)}
                            {--all : Purge ALL employees for that month (dangerous)}
                            {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Delete attendance records for a given month (scoped by emails or --all).';

    public function handle(): int
    {
        $monthInput = $this->option('month') ?: now()->format('Y-m');
        try {
            $month = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable $e) {
            $this->error("Invalid month '{$monthInput}'. Expected format: YYYY-MM (example: 2025-12).");
            return self::INVALID;
        }

        $startDate = $month->copy()->startOfMonth()->toDateString();
        $endDate = $month->copy()->endOfMonth()->toDateString();
        $dryRun = (bool) $this->option('dry-run');
        $purgeAll = (bool) $this->option('all');

        $emails = (array) $this->option('emails');
        if (!$purgeAll && empty($emails)) {
            $emails = (array) config('attendance.auto_present_emails', []);
        }

        if (!$purgeAll && empty($emails)) {
            $this->error('No scope provided. Pass --emails=... or set AUTO_PRESENT_EMAILS in .env, or use --all (dangerous).');
            return self::INVALID;
        }

        $employeeIds = [];
        if (!$purgeAll) {
            $employeeIds = Employee::query()
                ->whereIn('email', $emails)
                ->pluck('id')
                ->all();

            if (empty($employeeIds)) {
                $this->warn('No matching employees found for the provided emails. Nothing to delete.');
                return self::SUCCESS;
            }
        }

        $query = AttendanceEmployee::query()->whereBetween('date', [$startDate, $endDate]);
        if (!$purgeAll) {
            $query->whereIn('employee_id', $employeeIds);
        }

        $count = (clone $query)->count();
        $scopeText = $purgeAll ? 'ALL employees' : ('employees: ' . implode(', ', $emails));
        $this->info("Month: {$startDate} to {$endDate}");
        $this->info("Scope: {$scopeText}");
        $this->info("Rows matched: {$count}");

        if ($dryRun) {
            $this->info('DRY RUN (no DB deletes).');
            return self::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("DONE: deleted {$deleted} attendance rows.");

        return self::SUCCESS;
    }
}











