<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreChannelPartnerRequest;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\StoreHomeLoanRequest;
use App\Http\Requests\StoreListPropertyRequest;
use App\Http\Requests\StoreNewsletterRequest;
use App\Http\Requests\StorePropertyInquiryRequest;
use App\Services\EnquiryMailer;
use Illuminate\Http\RedirectResponse;
use Throwable;

class EnquiryController extends Controller
{
    public function __construct(
        private readonly EnquiryMailer $enquiryMailer,
    ) {}

    public function consultation(StoreConsultationRequest $request): RedirectResponse
    {
        return $this->send(
            'Consultation Request',
            $request->validated(),
            'Thank you! Your consultation request has been sent. We will contact you shortly.'
        );
    }

    public function newsletter(StoreNewsletterRequest $request): RedirectResponse
    {
        return $this->send(
            'Newsletter Subscription',
            $request->validated(),
            'Thank you for subscribing to our newsletter!'
        );
    }

    public function channelPartner(StoreChannelPartnerRequest $request): RedirectResponse
    {
        return $this->send(
            'Channel Partner Application',
            $request->validated(),
            'Thank you! Your channel partner application has been submitted.'
        );
    }

    public function listProperty(StoreListPropertyRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uploads = $request->file('property_image', []) ?? [];

        return $this->send(
            'List Property Enquiry',
            $validated,
            'Thank you! Your property details have been submitted for verification.',
            is_array($uploads) ? $uploads : [$uploads]
        );
    }

    public function homeLoan(StoreHomeLoanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $title = ! empty($validated['bank'])
            ? 'Home Loan Enquiry — '.$validated['bank']
            : 'Home Loan Enquiry';

        return $this->send(
            $title,
            $validated,
            'Thank you! Your home loan enquiry has been sent. Our team will get back to you soon.'
        );
    }

    public function propertyInquiry(StorePropertyInquiryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['name'])) {
            $validated['name'] = trim(($validated['first_name'] ?? '').' '.($validated['last_name'] ?? ''));
        }

        $title = ! empty($validated['property'])
            ? 'Property Inquiry — '.$validated['property']
            : 'Property Inquiry';

        return $this->send(
            $title,
            $validated,
            'Thank you! Your property inquiry has been sent successfully.'
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, \Illuminate\Http\UploadedFile>  $uploads
     */
    private function send(string $title, array $data, string $successMessage, array $uploads = []): RedirectResponse
    {
        $source = $data['source'] ?? null;

        try {
            $this->enquiryMailer->send($title, $data, is_string($source) ? $source : null, $uploads);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Sorry, we could not send your enquiry right now. Please try again or call us directly.');
        }

        return back()->with('success', $successMessage);
    }
}
