<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RepairEducationDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:repair-education-documents';

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
        $employees = Employee::whereNotNull('education_details')->get();
        
        foreach ($employees as $employee) {
            $educations = json_decode($employee->education_details, true);
            
            foreach ($educations as &$edu) {
                if (!empty($edu['document'])) {
                    $expectedPath = storage_path('app/public/education_documents/'.$edu['document']);
                    
                    if (!file_exists($expectedPath)) {
                        $this->error("Missing: ".$edu['document']);
                        $edu['document'] = null; // Remove reference to missing file
                    }
                }
            }
            
            $employee->education_details = json_encode($educations);
            $employee->save();
        }
        
        $this->info('Completed verification of '.$employees->count().' employee records');
    }
    
}
