<?php

namespace App\Console\Commands;

use App\Models\AttendanceEmployee;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendanceMail;
use Illuminate\Support\Facades\DB;

class MarkFullMonthPresent extends Command
{
    protected $signature = 'attendance:mark-full-month-present
                            {emails* : Employee emails to mark present for the full month}
                            {--month= : Month in YYYY-MM format (default: current month)}
                            {--dry-run : Show what would change without writing to DB}';

    protected $description = 'Mark selected employees as Present for every day of a month (upsert by employee_id + date).';

    public function handle(): int
    {
        $monthInput = $this->option('month') ?: now()->format('Y-m');
        $emails = (array) $this->argument('emails');
        $dryRun = (bool) $this->option('dry-run');

        if (empty($emails)) {
            $this->error('Please provide at least 1 employee email.');
            $this->line('Example: php artisan attendance:mark-full-month-present 2025-12 user1@x.com user2@x.com');
            return self::INVALID;
        }

        try {
            $month = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable $e) {
            $this->error("Invalid month '{$monthInput}'. Expected format: YYYY-MM (example: 2025-12).");
            return self::INVALID;
        }

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        // IMPORTANT: In CLI there is no Auth user, so Utility::getValByName() falls back to created_by=1.
        // For correctness, read settings by employee->created_by directly from DB.
        $timeCache = [];

        $employees = Employee::query()
            ->whereIn('email', $emails)
            ->get(['id', 'email', 'name', 'created_by']);

        $foundEmails = $employees->pluck('email')->all();
        $missing = array_values(array_diff($emails, $foundEmails));

        if (!empty($missing)) {
            $this->warn('These emails were not found in employees table and will be skipped:');
            foreach ($missing as $m) {
                $this->line(" - {$m}");
            }
        }

        if ($employees->isEmpty()) {
            $this->error('No matching employees found. Nothing to do.');
            return self::FAILURE;
        }

        $this->info("Month: {$startDate->toDateString()} to {$endDate->toDateString()}");
        $this->info('Clock-in/out is read from settings per employee company (created_by).');
        $this->info('Employees:');
        foreach ($employees as $emp) {
            $this->line(" - [{$emp->id}] {$emp->name} <{$emp->email}>");
        }

        $totalUpserts = 0;
        $daysInMonth = $startDate->diffInDays($endDate) + 1;

        foreach ($employees as $emp) {
            $createdBy = !empty($emp->created_by) ? (int) $emp->created_by : 1;
            if (!isset($timeCache[$createdBy])) {
                $rawStart = $this->getSettingForCompany($createdBy, 'company_start_time');
                $rawEnd = $this->getSettingForCompany($createdBy, 'company_end_time');
                $timeCache[$createdBy] = [
                    'start' => $this->normalizeTime($rawStart, '09:30:00'),
                    'end' => $this->normalizeTime($rawEnd, '18:00:00'),
                ];
            }

            $companyStart = $timeCache[$createdBy]['start'];
            $companyEnd = $timeCache[$createdBy]['end'];

            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                $payload = [
                    'status' => AttendanceEmployee::STATUS_PRESENT,
                    'clock_in' => $companyStart,
                    'clock_out' => $companyEnd,
                    'late' => '00:00:00',
                    'early_leaving' => '00:00:00',
                    'overtime' => '00:00:00',
                    'total_rest' => '00:00:00',
                    'created_by' => $createdBy,
                ];

                if (!$dryRun) {
                    AttendanceEmployee::updateOrCreate(
                        [
                            'employee_id' => $emp->id,
                            'date' => $d->toDateString(),
                        ],
                        $payload
                    );
                }

                $totalUpserts++;
            }
        }

        $mode = $dryRun ? 'DRY RUN (no DB writes)' : 'DONE';
        $this->info("{$mode}: upserted {$totalUpserts} attendance rows ({$employees->count()} employees × {$daysInMonth} days).");

        // Send email notification if 3 or more employees have full month present
        if (!$dryRun && $employees->count() >= 3) {
            $this->sendFullMonthPresentEmail($employees, $monthInput);
        }

        return self::SUCCESS;
    }

    private function getSettingForCompany(int $companyId, string $name): ?string
    {
        // Prefer the companyId row, fallback to created_by=1
        $val = DB::table('settings')
            ->where('created_by', $companyId)
            ->where('name', $name)
            ->value('value');

        if ($val === null) {
            $val = DB::table('settings')
                ->where('created_by', 1)
                ->where('name', $name)
                ->value('value');
        }

        return is_string($val) ? $val : null;
    }

    private function normalizeTime($value, string $fallback): string
    {
        $value = is_string($value) ? trim($value) : '';
        if ($value === '') {
            return $fallback;
        }

        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value . ':00';
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            return $value;
        }

        $ts = strtotime($value);
        if ($ts === false) {
            return $fallback;
        }

        return date('H:i:s', $ts);
    }

    /**
     * Send email notification for full month present employees
     */
    private function sendFullMonthPresentEmail($employees, $month)
    {
        try {
            $employeeNames = $employees->pluck('name')->toArray();
            $employeeEmails = $employees->pluck('email')->toArray();
            
            // Add additional notification emails to the recipient list
            $additionalEmails = [
                'dushyant@risingspaces.in',
                // Add more emails here as needed
            ];
            $recipientEmails = array_merge($employeeEmails, $additionalEmails);
            // Remove duplicates
            $recipientEmails = array_unique($recipientEmails);
            
            $attendanceData = [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                'employees' => $employeeNames,
                'employee_count' => $employees->count(),
            ];

            // Configure mail settings
            $companyId = $employees->first()->created_by ?? 1;
            Utility::getSMTPDetails($companyId);

            // Send email to all recipients
            foreach ($recipientEmails as $email) {
                Mail::to($email)->send(new AttendanceMail($attendanceData));
            }

            $this->info("Email notification sent to: " . implode(', ', $recipientEmails));
        } catch (\Exception $e) {
            $this->warn("Failed to send email notification: " . $e->getMessage());
        }
    }
}


