<?php

namespace App\Mail;

use App\Models\Brand;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrandTicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $brand;
    public ?Setting $setting;

    public function __construct(Ticket $ticket, Brand $brand)
    {
        $this->ticket = $ticket;
        $this->brand = $brand;
        $this->setting = Setting::where('branch_id', $ticket->branch_id ?? null)->first() ?? Setting::first();
    }

    public function envelope(): Envelope
    {
        $subject = 'Product Issue Reported: #' . ($this->ticket->ticket_no ?? $this->ticket->id);
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
            view: 'emails.brand_ticket_created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
