<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Services\PropertyReraSyncService;
use Illuminate\Console\Command;

class SyncPropertyReraCommand extends Command
{
    protected $signature = 'properties:sync-rera
                            {slug? : Sync a single property by slug}
                            {--all : Sync RERA IDs for all properties}
                            {--dry-run : Show what would change without saving}';

    protected $description = 'Fetch RERA IDs from yes2broker.in and update property overview data';

    public function handle(PropertyReraSyncService $syncService): int
    {
        $slugs = $this->resolveSlugs();

        if ($slugs === []) {
            $this->error('Provide a slug or use --all. Example: php artisan properties:sync-rera --all');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry run — no database changes will be saved.');
        }

        $stats = [
            'updated' => 0,
            'unchanged' => 0,
            'missing' => 0,
            'failed' => 0,
        ];

        $bar = $this->output->createProgressBar(count($slugs));
        $bar->start();

        foreach ($slugs as $slug) {
            $property = Property::query()->where('slug', $slug)->first();

            if ($property === null) {
                $stats['failed']++;
                $this->newLine();
                $this->warn("Not found: {$slug}");
                $bar->advance();

                continue;
            }

            $result = $syncService->syncProperty($property, $dryRun);
            $stats[$result['status']]++;

            if ($result['status'] === 'updated') {
                $this->newLine();
                $this->line("Updated {$slug}: {$result['rera_id']}");
            }

            $bar->advance();
            usleep(250000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Updated: {$stats['updated']} | Unchanged: {$stats['unchanged']} | No RERA on site: {$stats['missing']} | Failed: {$stats['failed']}");

        return self::SUCCESS;
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
