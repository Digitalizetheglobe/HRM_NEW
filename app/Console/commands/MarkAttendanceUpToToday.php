<?php

namespace App\Console\Commands;

use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\Resignation;
use App\Models\Termination;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkAttendanceUpToToday extends Command
{
    protected $signature = 'attendance:mark-up-to-today
                            {--month= : Month in YYYY-MM format (default: current month)}
                            {--emails=* : Employee emails to mark (default: config attendance.auto_present_emails)}
                            {--dry-run : Show what would change without writing to DB}';

    protected $description = 'Generate attendance from month start up to TODAY only (never future dates).';

    public function handle(): int
    {
        $monthInput = $this->option('month') ?: now()->format('Y-m');
        try {
            $month = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable $e) {
            $this->error("Invalid month '{$monthInput}'. Expected format: YYYY-MM (example: 2025-12).");
            return self::INVALID;
        }

        $today = now()->startOfDay();
        if ($month->gt($today)) {
            $this->error('Refusing to mark attendance for a future month.');
            return self::INVALID;
        }

        $startDate = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        $endDate = $endOfMonth->lt($today) ? $endOfMonth : $today;

        $emails = (array) $this->option('emails');
        if (empty($emails)) {
            $emails = (array) config('attendance.auto_present_emails', []);
        }
        if (empty($emails)) {
            $this->error('No emails provided. Pass --emails=... or set AUTO_PRESENT_EMAILS in .env');
            return self::INVALID;
        }

        $dryRun = (bool) $this->option('dry-run');

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

        $this->info("Range: {$startDate->toDateString()} to {$endDate->toDateString()} (up to TODAY only)");
        $this->info('Clock-in/out is read from settings per employee company (created_by).');

        $totalUpserts = 0;

        foreach ($employees as $emp) {
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

            $empStart = $startDate->copy();
            if (!empty($emp->company_doj)) {
                $doj = Carbon::parse($emp->company_doj)->startOfDay();
                if ($doj->gt($empStart)) {
                    $empStart = $doj;
                }
            }

            $empEnd = $endDate->copy();

            $termination = Termination::where('employee_id', $emp->id)
                ->whereDate('termination_date', '>=', $empStart->toDateString())
                ->whereDate('termination_date', '<=', $empEnd->toDateString())
                ->orderBy('termination_date', 'asc')
                ->first();

            $resignation = Resignation::where('employee_id', $emp->id)
                ->whereDate('resignation_date', '>=', $empStart->toDateString())
                ->whereDate('resignation_date', '<=', $empEnd->toDateString())
                ->orderBy('resignation_date', 'asc')
                ->first();

            if ($termination) {
                $empEnd = Carbon::parse($termination->termination_date)->startOfDay();
            } elseif ($resignation) {
                $empEnd = Carbon::parse($resignation->resignation_date)->startOfDay();
            }

            if ($empStart->gt($empEnd)) {
                continue;
            }

            for ($d = $empStart->copy(); $d->lte($empEnd); $d->addDay()) {
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
        $this->info("{$mode}: upserted {$totalUpserts} attendance rows.");

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


