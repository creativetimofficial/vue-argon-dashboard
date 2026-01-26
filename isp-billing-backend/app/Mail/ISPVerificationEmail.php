<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ISPVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $verificationUrl;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->verificationUrl = env('FRONTEND_URL', 'http://localhost:8080') . '/verify-email/' . $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your ISP Billing Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.isp-verification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
