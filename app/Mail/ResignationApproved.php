<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResignationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $resignation;

    public function __construct($resignation)
    {
        $this->resignation = $resignation;
    }

    public function build()
    {
        return $this->view('email.resignation_approved')
            ->with('resignation', $this->resignation)
            ->subject('Your Resignation Has Been Approved');
    }
}