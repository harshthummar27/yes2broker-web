<?php

namespace Database\Seeders;

use App\Services\Legacy\LegacyPropertyImporter;
use Illuminate\Database\Seeder;
use RuntimeException;

class LegacyPropertySeeder extends Seeder
{
    public function run(): void
    {
        $required = [
            base_path('properties_new.sql'),
            base_path('wp_posts.csv'),
            base_path('Properties-Export-2026-June-16-1029.csv'),
        ];

        foreach ($required as $file) {
            if (! is_file($file)) {
                throw new RuntimeException("Legacy import file missing: {$file}");
            }
        }

        $result = app(LegacyPropertyImporter::class)->import();

        if ($this->command) {
            $this->command->info("Legacy properties imported: {$result['imported']}");
            $this->command->info("With detail pages: {$result['with_detail_page']}");
        }
    }
}
