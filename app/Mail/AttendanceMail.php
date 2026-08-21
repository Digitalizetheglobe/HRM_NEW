<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $attendanceData;

    /**
     * Create a new message instance.
     *
     * @param array $attendanceData
     */
    public function __construct($attendanceData)
    {
        $this->attendanceData = $attendanceData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.attendance')
                    ->subject('Full Month Present - Attendance Update')
                    ->with('data', $this->attendanceData);
    }
}
