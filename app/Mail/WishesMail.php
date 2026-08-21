<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WishesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $eventType;      // 'birthday' | 'anniversary'
    public $recipientName;
    public $senderName;
    public $senderDesignation;
    public $companyName;
    public $customMessage;
    public $years;          // for anniversary
    public $eventDate;

    public function __construct(array $data)
    {
        $this->eventType        = $data['event_type'];
        $this->recipientName    = $data['recipient_name'];
        $this->senderName       = $data['sender_name'];
        $this->senderDesignation = $data['sender_designation'] ?? '';
        $this->companyName      = $data['company_name'] ?? config('app.name');
        $this->customMessage    = $data['custom_message'] ?? null;
        $this->years            = $data['years'] ?? null;
        $this->eventDate        = $data['event_date'] ?? null;
    }

    public function build()
    {
        $subject = $this->eventType === 'birthday'
            ? "Birthday Wishes from {$this->senderName}"
            : "Work Anniversary Wishes from {$this->senderName}";

        return $this
            ->subject($subject)
            ->view('email.wishes')
            ->with([
                'eventType'        => $this->eventType,
                'recipientName'    => $this->recipientName,
                'senderName'       => $this->senderName,
                'senderDesignation' => $this->senderDesignation,
                'companyName'      => $this->companyName,
                'customMessage'    => $this->customMessage,
                'years'            => $this->years,
                'eventDate'        => $this->eventDate,
            ]);
    }
}
