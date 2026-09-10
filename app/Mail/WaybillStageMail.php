<?php

namespace App\Mail;

use App\Models\Waybill;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaybillStageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Waybill $waybill,
        public string $actionType // 'pending_approval' or 'fully_approved'
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->actionType) {
            'pending_approval' => "URGENT: Waybill Action Required - {$this->waybill->waybill_no}",
            'fully_approved'   => "FINAL APPROVAL: Waybill Completed - {$this->waybill->waybill_no}",
        };

        return new Envelope(
            subject: $subject,
            using: [
                function ($message) {
                    $headers = $message->getHeaders();
                    $headers->addTextHeader('X-Priority', '1');
                    $headers->addTextHeader('X-MSMail-Priority', 'High');
                    $headers->addTextHeader('Importance', 'High');
                },
            ]
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.waybill-stage',
        );
    }
}
