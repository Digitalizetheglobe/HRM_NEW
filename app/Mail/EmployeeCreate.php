<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeCreate extends Mailable
{
    use Queueable, SerializesModels;
    
    public $user;
    public $employee;
    public $password;
    public $formattedEmployeeId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $employee, $password, $formattedEmployeeId = null)
    {
        $this->user = $user;
        $this->employee = $employee;
        $this->password = $password;
        $this->formattedEmployeeId = $formattedEmployeeId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.employee_create')
            ->subject('Welcome to ' . env('APP_NAME') . ' - Employee Account Created')
            ->with([
                'user' => $this->user,
                'employee' => $this->employee,
                'password' => $this->password,
                'formattedEmployeeId' => $this->formattedEmployeeId,
            ]);
    }
}

