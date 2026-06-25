<?php

namespace App\Mail;

use App\Models\TourEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public TourEnquiry $enquiry;

    public function __construct(TourEnquiry $enquiry)
    {
        $this->enquiry = $enquiry;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Tour Enquiry Received'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tour-enquiry',
            with: [
                'enquiry' => $this->enquiry
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}