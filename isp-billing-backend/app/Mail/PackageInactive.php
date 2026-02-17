<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PackageInactive extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $order;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $reason = 'cancelled')
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Layanan Nonaktif: ' . ($this->order->service_name ?? 'Paket Langganan');
        if ($this->reason === 'expired') {
            $subject = 'Layanan Berakhir: ' . ($this->order->service_name ?? 'Paket Langganan');
        }
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $url = config('app.frontend_url') . '/client-area/services/' . $this->order->id;

        // Magic Link Logic
        $user = \App\Models\User::where('email', $this->order->isp->email)->first();
        if ($user) {
            $url = \Illuminate\Support\Facades\URL::signedRoute(
                'auth.magic-login', 
                [
                    'user' => $user->id, 
                    'redirect' => '/client-area/services/' . $this->order->id
                ],
                now()->addHours(24)
            );
        }

        return new Content(
            view: 'emails.package_inactive',
            with: [
                'order' => $this->order,
                'isp' => $this->order->isp,
                'reason' => $this->reason,
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
