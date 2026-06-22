<?php

namespace App\Console\Commands;

use App\Services\Legacy\LegacyPropertyImporter;
use Illuminate\Console\Command;

class ImportLegacyPropertiesCommand extends Command
{
    protected $signature = 'properties:import-legacy';

    protected $description = 'Import properties from properties_new.sql + wp_posts.csv + Properties export CSV';

    public function handle(LegacyPropertyImporter $importer): int
    {
        if (! config('media-import.import_enabled', false)) {
            $this->error('WordPress import is disabled. Set WORDPRESS_IMPORT_ENABLED=true in .env to use this command.');

            return self::FAILURE;
        }

        $this->info('Importing properties from legacy WordPress export files...');
        $this->newLine();
        $this->line('Sources:');
        $this->line('  - properties_new.sql (base listing data)');
        $this->line('  - wp_posts.csv (detail pages + gallery attachments)');
        $this->line('  - Properties-Export-2026-June-16-1029.csv (listing excerpts)');
        $this->newLine();

        $bar = $this->output->createProgressBar(290);
        $bar->start();

        $result = $importer->import(function () use ($bar): void {
            $bar->advance();
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Imported: {$result['imported']}");
        $this->info("With detail page content: {$result['with_detail_page']}");
        $this->line("Skipped: {$result['skipped']}");
        $this->line("Failed: {$result['failed']}");

        return self::SUCCESS;
    }
}
