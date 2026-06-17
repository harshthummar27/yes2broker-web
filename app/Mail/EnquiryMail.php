<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, string>  $fields
     * @param  array<int, UploadedFile>  $uploads
     */
    public function __construct(
        public string $formTitle,
        public array $fields,
        public ?string $source = null,
        public array $uploads = [],
    ) {}

    public function envelope(): Envelope
    {
        $replyTo = $this->replyToAddress();

        return new Envelope(
            subject: 'New '.$this->formTitle.' — '.config('site.name'),
            replyTo: $replyTo ? [$replyTo] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiry',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return collect($this->uploads)
            ->filter(fn (UploadedFile $file) => $file->isValid())
            ->map(fn (UploadedFile $file) => Attachment::fromPath($file->getRealPath())
                ->as($file->getClientOriginalName())
                ->withMime($file->getMimeType() ?? 'application/octet-stream'))
            ->values()
            ->all();
    }

    private function replyToAddress(): ?Address
    {
        $email = $this->fields['Email'] ?? $this->fields['email'] ?? null;

        if (! is_string($email) || $email === '') {
            return null;
        }

        $name = $this->fields['Name'] ?? $this->fields['name'] ?? null;

        if (! is_string($name) || trim($name) === '') {
            $firstName = $this->fields['First Name'] ?? $this->fields['first_name'] ?? '';
            $lastName = $this->fields['Last Name'] ?? $this->fields['last_name'] ?? '';
            $name = trim($firstName.' '.$lastName) ?: null;
        }

        return new Address($email, is_string($name) ? $name : '');
    }
}
