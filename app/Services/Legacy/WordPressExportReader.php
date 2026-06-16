<?php

namespace App\Services\Legacy;

use RuntimeException;

class WordPressExportReader
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function indexBySlug(): array
    {
        $path = base_path('Properties-Export-2026-June-16-1029.csv');

        if (! is_file($path)) {
            throw new RuntimeException('Properties-Export CSV not found in project root.');
        }

        $records = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException('Unable to open Properties export CSV.');
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);

            throw new RuntimeException('Properties export CSV is empty.');
        }

        $headers = $this->normalizeHeaders($headers);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 5) {
                continue;
            }

            $slugIndex = $this->findHeaderIndex($headers, 'Slug');

            if ($slugIndex === null) {
                continue;
            }

            $slug = trim((string) ($row[$slugIndex] ?? ''));

            if ($slug === '') {
                continue;
            }

            $record = [];

            foreach ($headers as $index => $header) {
                $record[$header] = $row[$index] ?? null;
            }

            $records[$slug] = $record;
        }

        fclose($handle);

        return $records;
    }

    /**
     * @param  list<string|null>  $headers
     * @return list<string>
     */
    private function normalizeHeaders(array $headers): array
    {
        $seen = [];
        $normalized = [];

        foreach ($headers as $header) {
            $name = trim((string) $header);

            if ($name === '') {
                $name = 'column_'.count($normalized);
            }

            if (isset($seen[$name])) {
                $seen[$name]++;
                $name .= '_'.$seen[$name];
            } else {
                $seen[$name] = 1;
            }

            $normalized[] = $name;
        }

        return $normalized;
    }

    /**
     * @param  list<string>  $headers
     */
    private function findHeaderIndex(array $headers, string $needle): ?int
    {
        foreach ($headers as $index => $header) {
            if ($header === $needle) {
                return $index;
            }
        }

        return null;
    }
}
