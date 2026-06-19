<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Throwable;

class EnquiryDispatcher
{
    public function __construct(
        private readonly EnquiryMailer $enquiryMailer,
        private readonly LeadPlusCrmService $leadPlusCrm,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $uploads
     */
    public function dispatch(string $formTitle, array $data, ?string $source = null, array $uploads = []): void
    {
        $this->enquiryMailer->send($formTitle, $data, $source, $uploads);

        try {
            $this->leadPlusCrm->submitLead($formTitle, $data, $source);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
