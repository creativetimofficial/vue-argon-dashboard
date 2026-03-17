<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Withdrawal;
use App\Models\Isp;

class WithdrawalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;
    public $isp;
    public $isAdminWarning;

    /**
     * Create a new message instance.
     */
    public function __construct(Withdrawal $withdrawal, Isp $isp, $isAdminWarning = false)
    {
        $this->withdrawal = $withdrawal;
        $this->isp = $isp;
        $this->isAdminWarning = $isAdminWarning;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusLabel = 'PENDING';
        if ($this->withdrawal->status === 'approved') $statusLabel = 'BERHASIL';
        if ($this->withdrawal->status === 'rejected') $statusLabel = 'GAGAL';

        $subject = $this->isAdminWarning 
            ? "[{$statusLabel}] Peringatan Keamanan: Penarikan Dana ISP " . $this->isp->company_name 
            : "[{$statusLabel}] Pemberitahuan Pencairan Dana - Billing ISP";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.financial.withdrawal',
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
