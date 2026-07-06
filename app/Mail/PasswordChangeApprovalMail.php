<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class PasswordChangeApprovalMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $approveUrl,
        public string $rejectUrl,
        public Carbon $expiresAt,
        public bool $forgotCurrentPassword = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admin Password Change Approval — '.config('site.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-change-approval',
        );
    }
}
