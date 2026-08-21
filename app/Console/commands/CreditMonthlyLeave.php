<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreditMonthlyLeave extends Command
{
    protected $signature = 'leave:credit';
    protected $description = 'Credit monthly leave to all employees';
    
    public function handle()
    {
        $url = config('app.url') . '/cron/credit-leave';
        $response = Http::get($url);
        
        if ($response->successful()) {
            $this->info('Monthly leave credited successfully.');
        } else {
            $this->error('Failed to credit monthly leave.');
        }
    }
}