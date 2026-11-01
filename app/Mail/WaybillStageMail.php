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

    private const ACTION_TYPES = ['pending_approval', 'fully_approved'];

    public int $tries = 3;

    public array $backoff = [60, 300];

    public int $timeout = 30;

    public string $stageStatus;

    public function __construct(
        public Waybill $waybill,
        public string $actionType
    ) {
        if (! in_array($actionType, self::ACTION_TYPES, true)) {
            throw new \InvalidArgumentException("Unsupported waybill notification action type: {$actionType}");
        }
        $this->stageStatus = $waybill->status;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->actionType) {
            'pending_approval' => "URGENT: Waybill Action Required - {$this->waybill->waybill_no}",
            'fully_approved' => "FINAL APPROVAL: Waybill Completed - {$this->waybill->waybill_no}",
            default => throw new \InvalidArgumentException("Unsupported waybill notification action type: {$this->actionType}"),
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
