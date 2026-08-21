<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixLoanAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-loan-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
   public function handle()
    {
        $loans = EmployeeLoan::with('deductions')->get();
        
        foreach ($loans as $loan) {
            $totalDeducted = $loan->deductions()
                ->where('is_deducted', true)
                ->sum('emi_amount');
                
            $loan->remaining_amount = $loan->total_amount - $totalDeducted;
            $loan->save();
            
            $this->info("Updated loan {$loan->id}: Remaining = {$loan->remaining_amount}");
        }
        
        $this->info('All loan amounts fixed!');
    }
}
