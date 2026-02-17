<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $invoice;
    public $isp;

    /**
     * Create a new message instance.
     */
    public function __construct($invoice, $isp)
    {
        $this->invoice = $invoice;
        $this->isp = $isp;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tagihan Baru #' . $this->invoice->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $url = config('app.frontend_url') . '/client-area/invoices/' . $this->invoice->id;

        // Magic Link Logic
        $user = \App\Models\User::where('email', $this->isp->email)->first();
        if ($user) {
            $url = \Illuminate\Support\Facades\URL::signedRoute(
                'auth.magic-login', 
                [
                    'user' => $user->id, 
                    'redirect' => '/client-area/invoices/' . $this->invoice->id
                ],
                now()->addHours(24) // 24 hour expiry
            );
        }

        return new Content(
            view: 'emails.invoice_created',
            with: [
                'invoice' => $this->invoice,
                'isp' => $this->isp,
                'url' => $url,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
