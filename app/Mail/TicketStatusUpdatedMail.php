<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public string $oldStatus;
    public string $recipientName;
    public string $recipientType;
    public ?Setting $setting;

    public function __construct(Ticket $ticket, string $oldStatus, string $recipientName, string $recipientType = 'customer')
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
        $this->recipientName = $recipientName;
        $this->recipientType = $recipientType;
        $this->setting = Setting::where('branch_id', $ticket->branch_id ?? null)->first() ?? Setting::first();
    }

    public function envelope(): Envelope
    {
        $subject = 'Ticket Status Updated: #' . ($this->ticket->ticket_no ?? $this->ticket->id);
        $fromAddress = config('mail.from.address');
        $fromName = $this->setting?->name ?? config('mail.from.name');

        if (! empty($fromAddress)) {
            return new Envelope(
                from: new Address($fromAddress, $fromName),
                subject: $subject,
            );
        }

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket_status_updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
