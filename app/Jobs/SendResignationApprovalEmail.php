<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ResignationApproved;
use App\Models\Utility;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SendResignationApprovalEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $resignation;
    protected $email;
    protected $companyId;

    public function __construct($resignation, $email, $companyId = null)
    {
        $this->resignation = $resignation;
        $this->email = $email;
        $this->companyId = $companyId;
    }

    public function handle()
    {
        // Set a reasonable timeout just for this job
        set_time_limit(60);

        // Load company SMTP settings (best-effort). Never block approval if SMTP is misconfigured.
        try {
            if (!empty($this->companyId)) {
                Utility::getSMTPDetails($this->companyId);
            }
        } catch (\Throwable $e) {
            Log::warning('Resignation approval email: failed to load SMTP settings.', [
                'company_id' => $this->companyId,
                'resignation_id' => data_get($this->resignation, 'id'),
                'error' => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($this->email)->send(new ResignationApproved($this->resignation));
        } catch (TransportExceptionInterface $e) {
            Log::error('Resignation approval email: SMTP transport error.', [
                'company_id' => $this->companyId,
                'resignation_id' => data_get($this->resignation, 'id'),
                'to' => $this->email,
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Resignation approval email: unexpected error.', [
                'company_id' => $this->companyId,
                'resignation_id' => data_get($this->resignation, 'id'),
                'to' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}