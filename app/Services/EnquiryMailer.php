<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\EnquiryMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class EnquiryMailer
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $uploads
     */
    public function send(string $formTitle, array $data, ?string $source = null, array $uploads = []): void
    {
        $fields = $this->formatFields($data);
        $recipient = (string) config('mail.enquiry_to');

        Mail::to($recipient)->send(new EnquiryMail($formTitle, $fields, $source, $uploads));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private function formatFields(array $data): array
    {
        $fields = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['_token', 'source', 'terms', 'verification'], true)) {
                continue;
            }

            if (is_array($value)) {
                $fields[$this->labelize((string) $key)] = implode(', ', array_map('strval', $value));

                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $fields[$this->labelize((string) $key)] = (string) $value;
        }

        return $fields;
    }

    private function labelize(string $key): string
    {
        return ucwords(str_replace('_', ' ', $key));
    }
}
