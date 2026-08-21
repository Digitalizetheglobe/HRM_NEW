<?php

namespace App\Console\Commands;

use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\Resignation;
use App\Models\Termination;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkAttendanceTodayFromConfig extends Command
{
    protected $signature = 'attendance:mark-today
                            {--dry-run : Show what would change without writing to DB}';

    protected $description = 'Daily automation: mark ONLY today for employees configured in AUTO_PRESENT_EMAILS.';

    public function handle(): int
    {
        $emails = (array) config('attendance.auto_present_emails', []);
        if (empty($emails)) {
            $this->warn('No emails configured. Set env AUTO_PRESENT_EMAILS="a@x.com,b@x.com".');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');

        $today = Carbon::today();
        // IMPORTANT: In CLI there is no Auth user, so Utility::getValByName() falls back to created_by=1.
        // For correctness, read settings by employee->created_by directly from DB.
        $timeCache = [];

        $employees = Employee::query()
            ->whereIn('email', $emails)
            ->get(['id', 'email', 'name', 'created_by', 'company_doj']);

        if ($employees->isEmpty()) {
            $this->warn('No matching employees found. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info('Date: ' . $today->toDateString());

        $upserts = 0;

        foreach ($employees as $emp) {
            // Joining date gate
            if (!empty($emp->company_doj) && Carbon::parse($emp->company_doj)->startOfDay()->gt($today)) {
                continue;
            }

            // Termination/resignation gate
            $terminated = Termination::where('employee_id', $emp->id)
                ->whereDate('termination_date', '<=', $today->toDateString())
                ->exists();
            $resigned = Resignation::where('employee_id', $emp->id)
                ->whereDate('resignation_date', '<=', $today->toDateString())
                ->exists();
            if ($terminated || $resigned) {
                continue;
            }

            $createdBy = !empty($emp->created_by) ? (int) $emp->created_by : 1;

            if (!isset($timeCache[$createdBy])) {
                $rawStart = $this->getSettingForCompany($createdBy, 'company_start_time');
                $rawEnd = $this->getSettingForCompany($createdBy, 'company_end_time');
                $timeCache[$createdBy] = [
                    'start' => $this->normalizeTime($rawStart, '09:00:00'),
                    'end' => $this->normalizeTime($rawEnd, '18:00:00'),
                ];
            }

            $companyStart = $timeCache[$createdBy]['start'];
            $companyEnd = $timeCache[$createdBy]['end'];

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
                        'date' => $today->toDateString(),
                    ],
                    $payload
                );
            }

            $upserts++;
        }

        $mode = $dryRun ? 'DRY RUN (no DB writes)' : 'DONE';
        $this->info("{$mode}: upserted {$upserts} attendance rows for today.");

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
}


