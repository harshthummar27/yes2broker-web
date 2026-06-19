<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeadPlusCrmService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function submitLead(string $formTitle, array $data, ?string $source = null): bool
    {
        if (! config('crm.leadplus.enabled')) {
            Log::info('LeadPlus CRM is disabled. Set LEADPLUS_ENABLED=true in .env to sync leads.');

            return true;
        }

        $vendorKey = config('crm.leadplus.vendor_key');

        if (! is_string($vendorKey) || $vendorKey === '') {
            Log::warning('LeadPlus CRM skipped: LEADPLUS_VENDOR_KEY is not configured.');

            return false;
        }

        $payload = $this->buildPayload($formTitle, $data, $source, $vendorKey);

        try {
            $response = Http::timeout((int) config('crm.leadplus.timeout', 15))
                ->acceptJson()
                ->asJson()
                ->post((string) config('crm.leadplus.url'), $payload);

            if ($response->successful() && $this->responseIndicatesSuccess($response->body())) {
                Log::info('LeadPlus CRM lead submitted.', [
                    'form' => $formTitle,
                    'source' => $source,
                ]);

                return true;
            }

            Log::error('LeadPlus CRM submission failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $this->redactPayload($payload),
            ]);

            return false;
        } catch (Throwable $exception) {
            Log::error('LeadPlus CRM submission error.', [
                'message' => $exception->getMessage(),
                'payload' => $this->redactPayload($payload),
            ]);

            report($exception);

            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private function buildPayload(string $formTitle, array $data, ?string $source, string $vendorKey): array
    {
        $name = $this->parseName($data);
        $propertyContext = $this->resolvePropertyContext($formTitle, $data);
        $message = $this->buildMessage($formTitle, $data, $propertyContext);

        return [
            'FirstName' => $name['first'],
            'LastName' => $name['last'],
            'ISD' => (string) config('crm.leadplus.isd', '91'),
            'Phone' => $this->resolvePhone($data),
            'EmailId' => (string) ($data['email'] ?? ''),
            'State' => $this->stringValue($data['state'] ?? config('crm.leadplus.default_state')),
            'City' => $this->stringValue($data['city'] ?? config('crm.leadplus.default_city')),
            'Location' => $this->resolveLocation($data),
            'Project' => $this->resolveProject($data),
            'PropertyFor' => $propertyContext['propertyFor'],
            'Property' => $propertyContext['property'],
            'PropertyType' => $propertyContext['propertyType'],
            'Message' => $message,
            'LeadSource' => $source ?: (string) config('crm.leadplus.default_lead_source', 'Website'),
            'vendor_key' => $vendorKey,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{first: string, last: string}
     */
    private function parseName(array $data): array
    {
        if (! empty($data['first_name'])) {
            return [
                'first' => trim((string) $data['first_name']),
                'last' => trim((string) ($data['last_name'] ?? '')),
            ];
        }

        $fullName = trim((string) ($data['name'] ?? ''));

        if ($fullName === '') {
            return ['first' => 'Website', 'last' => 'Lead'];
        }

        $parts = preg_split('/\s+/', $fullName, 2) ?: [];

        return [
            'first' => $parts[0] ?? $fullName,
            'last' => $parts[1] ?? '',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolvePhone(array $data): string
    {
        return (string) ($data['phone'] ?? $data['mobile'] ?? '');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveLocation(array $data): string
    {
        foreach (['property_address', 'full_address', 'location', 'area'] as $field) {
            $value = $this->stringValue($data[$field] ?? '');

            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveProject(array $data): string
    {
        foreach (['property', 'property_title', 'project', 'bank'] as $field) {
            $value = $this->stringValue($data[$field] ?? '');

            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{propertyFor: string, property: string, propertyType: string}
     */
    private function resolvePropertyContext(string $formTitle, array $data): array
    {
        $propertyFor = 'Buy';
        $property = '';
        $propertyType = '';

        if (str_contains(strtolower($formTitle), 'list property')) {
            $propertyFor = ($data['status'] ?? '') === 'for_rent' ? 'Rent' : 'Sell';
            $property = $this->mapPropertyCategory((string) ($data['type'] ?? ''));
        } elseif (str_contains(strtolower($formTitle), 'channel partner')) {
            $propertyFor = 'Partner';
        } elseif (str_contains(strtolower($formTitle), 'newsletter')) {
            $propertyFor = 'Subscribe';
        } elseif (str_contains(strtolower($formTitle), 'home loan')) {
            $propertyFor = 'Buy';
        }

        if (! empty($data['looking_for'])) {
            $mapped = $this->mapLookingFor((string) $data['looking_for']);
            $property = $mapped['property'];
            $propertyType = $mapped['propertyType'];
        } elseif (! empty($data['type']) && $property === '') {
            $property = $this->mapPropertyCategory((string) $data['type']);
        }

        if ($propertyType === '' && ! empty($data['bhk'])) {
            $propertyType = (string) $data['bhk'];
        }

        return [
            'propertyFor' => $propertyFor,
            'property' => $property,
            'propertyType' => $propertyType,
        ];
    }

    /**
     * @return array{property: string, propertyType: string}
     */
    private function mapLookingFor(string $lookingFor): array
    {
        return match (trim($lookingFor)) {
            'Apartment - 1 BHK' => ['property' => 'Flat', 'propertyType' => '1 BHK'],
            'Apartment - 2 BHK' => ['property' => 'Flat', 'propertyType' => '2 BHK'],
            'Apartment - 3 BHK' => ['property' => 'Flat', 'propertyType' => '3 BHK'],
            'Commercial Space' => ['property' => 'Commercial', 'propertyType' => ''],
            'Plot' => ['property' => 'Plot', 'propertyType' => ''],
            'Bungalow' => ['property' => 'Bungalow', 'propertyType' => ''],
            default => ['property' => '', 'propertyType' => $lookingFor],
        };
    }

    private function mapPropertyCategory(string $type): string
    {
        $normalized = strtolower(trim($type));

        if ($normalized === '') {
            return '';
        }

        if (str_contains($normalized, 'apartment') || str_contains($normalized, 'flat')) {
            return 'Flat';
        }

        if (str_contains($normalized, 'villa')) {
            return 'Villa';
        }

        if (str_contains($normalized, 'bungalow')) {
            return 'Bungalow';
        }

        if (str_contains($normalized, 'office') || str_contains($normalized, 'showroom') || str_contains($normalized, 'commercial')) {
            return 'Commercial';
        }

        if (str_contains($normalized, 'land') || str_contains($normalized, 'plot')) {
            return 'Plot';
        }

        if (str_contains($normalized, 'farmhouse')) {
            return 'Farmhouse';
        }

        return $type;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array{propertyFor: string, property: string, propertyType: string}  $propertyContext
     */
    private function buildMessage(string $formTitle, array $data, array $propertyContext): string
    {
        $lines = ["Form: {$formTitle}"];

        $skip = [
            '_token',
            'source',
            'terms',
            'verification',
            'property_image',
            'first_name',
            'last_name',
            'name',
            'phone',
            'mobile',
            'alternate_phone',
            'email',
            'city',
            'state',
            'property_address',
            'full_address',
            'location',
            'area',
            'property',
            'property_title',
            'project',
            'looking_for',
            'type',
            'status',
            'message',
            'remark',
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $skip, true) || $value === null || $value === '' || is_array($value)) {
                continue;
            }

            $lines[] = ucwords(str_replace('_', ' ', (string) $key)).': '.(string) $value;
        }

        if (! empty($data['message'])) {
            $lines[] = 'Message: '.(string) $data['message'];
        }

        if (! empty($data['remark'])) {
            $lines[] = 'Remark: '.(string) $data['remark'];
        }

        if ($propertyContext['propertyFor'] !== '') {
            $lines[] = 'Property For: '.$propertyContext['propertyFor'];
        }

        return implode("\n", $lines);
    }

    private function stringValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    /**
     * @param  array<string, string>  $payload
     * @return array<string, string>
     */
    private function redactPayload(array $payload): array
    {
        if (isset($payload['vendor_key'])) {
            $payload['vendor_key'] = '***';
        }

        return $payload;
    }

    private function responseIndicatesSuccess(string $body): bool
    {
        if ($body === '') {
            return true;
        }

        $decoded = json_decode($body, true);

        if (! is_array($decoded)) {
            return true;
        }

        $statusCode = strtolower((string) ($decoded['statusCode'] ?? $decoded['StatusCode'] ?? ''));

        if ($statusCode === '') {
            return true;
        }

        foreach (['invalid', 'error', 'fail', 'denied', 'reject'] as $indicator) {
            if (str_contains($statusCode, $indicator)) {
                return false;
            }
        }

        return true;
    }
}
