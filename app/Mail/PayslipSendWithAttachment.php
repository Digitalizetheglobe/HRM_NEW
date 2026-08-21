<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayslipSendWithAttachment extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $settings;
    public $mailTo;
    public $pdfContent;
    public $pdfFileName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template, $settings, $mailTo, $pdfContent, $pdfFileName)
    {
        $this->template = $template;
        $this->settings = $settings;
        $this->mailTo = $mailTo;
        $this->pdfContent = $pdfContent;
        $this->pdfFileName = $pdfFileName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from($this->settings['mail_from_address'], $this->settings['mail_from_name'])
            ->markdown('email.common_email_template')
            ->subject($this->template->subject)
            ->with('content', $this->template->content);

        // Attach PDF
        if ($this->pdfContent) {
            $mail->attachData($this->pdfContent, $this->pdfFileName, [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}




