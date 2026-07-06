<?php

namespace App\Services;

use App\Models\Property;

class PropertyReraSyncService
{
    private const WP_BASE = 'https://yes2broker.in';

    public function __construct(
        private readonly PropertyPageHtmlParser $htmlParser,
    ) {}

    /**
     * @return array{status: 'updated'|'unchanged'|'missing'|'failed', rera_id: ?string}
     */
    public function syncProperty(Property $property, bool $dryRun = false): array
    {
        $html = @file_get_contents(self::WP_BASE.'/'.$property->slug.'/');

        if ($html === false) {
            return ['status' => 'failed', 'rera_id' => null];
        }

        $reraId = $this->htmlParser->extractReraId($html);

        if ($reraId === null) {
            $overview = $property->overview ?? [];
            $current = $overview['rera_id'] ?? null;

            if ($this->shouldResetReraPlaceholder($current)) {
                if (! $dryRun) {
                    $overview['rera_id'] = 'Available on request';
                    $property->overview = $overview;
                    $property->saveQuietly();
                }

                return ['status' => 'updated', 'rera_id' => 'Available on request'];
            }

            return ['status' => 'missing', 'rera_id' => null];
        }

        $overview = $property->overview ?? [];

        if (($overview['rera_id'] ?? null) === $reraId) {
            return ['status' => 'unchanged', 'rera_id' => $reraId];
        }

        if (! $dryRun) {
            $overview['rera_id'] = $reraId;
            $property->overview = $overview;
            $property->saveQuietly();
        }

        return ['status' => 'updated', 'rera_id' => $reraId];
    }

    private function shouldResetReraPlaceholder(?string $reraId): bool
    {
        if (blank($reraId)) {
            return false;
        }

        $normalized = strtolower(trim($reraId));

        if (str_contains($normalized, 'available on request')) {
            return false;
        }

        return str_starts_with(strtoupper(trim($reraId)), 'PR/GJ');
    }
}
