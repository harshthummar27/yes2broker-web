<?php

namespace App\Console\Commands;

use App\Services\Legacy\CsvPropertyImportService;
use Illuminate\Console\Command;

class ImportCsvPropertiesCommand extends Command
{
    protected $signature = 'properties:import-csv
                            {--path= : Optional path to Properties export CSV}';

    protected $description = 'Import property listing and detail data from Properties-Export CSV into the database';

    public function handle(CsvPropertyImportService $importer): int
    {
        $path = $this->option('path') ?: base_path('Properties-Export-2026-June-16-1029.csv');

        if (! is_file($path)) {
            $this->error("CSV not found: {$path}");

            return self::FAILURE;
        }

        $this->info('Importing properties from CSV...');
        $this->line("Source: {$path}");
        $this->newLine();
        $this->line('Listing fields (location, BHK, area, possession, price) come from the CSV excerpt.');
        $this->line('Rich detail (description, amenities, FAQs) comes from PropertyDetailData where available.');
        $this->line('Existing local images in storage are preserved, then re-synced from disk.');
        $this->newLine();

        $bar = $this->output->createProgressBar(290);
        $bar->start();

        $result = $importer->import(function () use ($bar): void {
            $bar->advance();
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Imported/updated: {$result['imported']}");
        $this->info("Local images preserved: {$result['images_preserved']}");
        $this->line("Skipped: {$result['skipped']}");

        return self::SUCCESS;
    }
}
