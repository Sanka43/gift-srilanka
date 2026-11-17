<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact,
        public string $replyBody
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->contact->subject . ' – ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-reply',
        );
    }
}
