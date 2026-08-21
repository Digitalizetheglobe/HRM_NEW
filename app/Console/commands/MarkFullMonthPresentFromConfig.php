<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkFullMonthPresentFromConfig extends Command
{
    protected $signature = 'attendance:mark-full-month-present-config
                            {--month= : Month in YYYY-MM format (default: previous month)}
                            {--dry-run : Show what would change without writing to DB}';

    protected $description = 'Mark employees (from config MONTHLY_FULL_PRESENT_EMAILS) as Present for the full month. Intended for scheduler.';

    public function handle(): int
    {
        $emails = config('attendance.monthly_full_present_emails', []);
        if (empty($emails)) {
            $this->warn('No emails configured. Set env MONTHLY_FULL_PRESENT_EMAILS="a@x.com,b@x.com".');
            return self::SUCCESS;
        }

        $monthInput = $this->option('month');
        $month = null;

        if ($monthInput) {
            try {
                $month = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
            } catch (\Throwable $e) {
                $this->error("Invalid month '{$monthInput}'. Expected format: YYYY-MM (example: 2025-12).");
                return self::INVALID;
            }
        } else {
            // Scheduler default: process PREVIOUS month on 1st of next month
            $month = now()->startOfMonth()->subMonth();
        }

        $dryRun = (bool) $this->option('dry-run');

        $args = $emails;
        $cmd = 'attendance:mark-full-month-present';
        $opts = [
            '--month' => $month->format('Y-m'),
        ];
        if ($dryRun) {
            $opts['--dry-run'] = true;
        }

        $this->info('Running: ' . $cmd . ' --month=' . $opts['--month'] . ($dryRun ? ' --dry-run' : ''));

        // Call the existing command
        $exit = $this->call($cmd, array_merge($opts, ['emails' => $args]));

        return $exit;
    }
}











