<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Services\WordPressPropertyImporter;
use Illuminate\Console\Command;

class ImportWordPressPropertyCommand extends Command
{
    protected $signature = 'properties:import-wp
                            {slug? : Property slug to import from yes2broker.in}
                            {--all : Import all properties that exist in the database}';

    protected $description = 'Import rich property detail (gallery, map, street view, FAQs) from yes2broker.in';

    public function handle(WordPressPropertyImporter $importer): int
    {
        $slugs = $this->resolveSlugs();

        if ($slugs === []) {
            $this->error('Provide a slug or use --all. Example: php artisan properties:import-wp elenza-gradient');

            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($slugs));
        $bar->start();

        $imported = 0;
        $failed = 0;

        foreach ($slugs as $slug) {
            $property = $importer->import($slug);

            if ($property === null) {
                $failed++;
                $this->newLine();
                $this->warn("Failed: {$slug}");
            } else {
                $imported++;
            }

            $bar->advance();
            usleep(300000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Imported: {$imported} | Failed: {$failed}");

        return $failed > 0 && $imported === 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function resolveSlugs(): array
    {
        if ($slug = $this->argument('slug')) {
            return [$slug];
        }

        if ($this->option('all')) {
            return Property::query()->orderBy('title')->pluck('slug')->all();
        }

        return [];
    }
}
