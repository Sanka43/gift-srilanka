<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackInStockMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public string $email
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->product->name . ' is back in stock – Gifts Sri Lanka',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.back-in-stock',
        );
    }
}
