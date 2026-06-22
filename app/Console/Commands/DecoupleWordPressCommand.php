<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Property;
use App\Services\MediaMigrationService;
use App\Services\SeedMediaSyncService;
use Illuminate\Console\Command;

class DecoupleWordPressCommand extends Command
{
    protected $signature = 'wordpress:decouple
        {--skip-migrate : Skip downloading property media}
        {--skip-seed-sync : Skip rewriting seed data files from the database}';

    protected $description = 'Remove WordPress media dependencies (database + seed files)';

    public function handle(
        MediaMigrationService $migration,
        SeedMediaSyncService $seedSync,
    ): int {
        $this->info('Step 1/4 — Migrating property images to local storage...');

        if ($this->option('skip-migrate')) {
            $this->warn('Skipped media migration.');
        } else {
            $stats = $migration->migrateProperties(dryRun: false);
            $this->line("Properties processed: {$stats['properties']}");
            $this->line("Downloaded: {$stats['downloaded']} | Skipped: {$stats['skipped']} | Failed: {$stats['failed']}");

            foreach ($stats['errors'] as $error) {
                $this->error("  {$error}");
            }

            $this->callSilent('storage:link');
        }

        $this->newLine();
        $this->info('Step 2/4 — Syncing seed data files from database...');

        if ($this->option('skip-seed-sync')) {
            $this->warn('Skipped seed file sync.');
        } else {
            $synced = $seedSync->syncFromDatabase();
            $this->line("PropertiesPageData image URLs updated: {$synced['properties']}");
            $this->line("PropertyDetailData gallery URLs updated: {$synced['details']}");
        }

        $this->newLine();
        $this->info('Step 3/4 — Verifying WordPress dependencies...');

        $issues = $this->collectIssues();

        if ($issues === []) {
            $this->info('No WordPress media dependencies found.');
        } else {
            foreach ($issues as $issue) {
                $this->warn($issue);
            }
        }

        $this->newLine();
        $this->info('Step 4/4 — Summary');
        $this->table(
            ['Check', 'Status'],
            [
                ['Database property images', $this->databaseStatus()],
                ['Seed data files', $this->seedFileStatus()],
                ['Site branding assets', $this->siteAssetStatus()],
            ]
        );

        return $issues === [] ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @return list<string>
     */
    private function collectIssues(): array
    {
        $issues = [];

        $dbCount = Property::query()
            ->where(function ($query): void {
                $query->where('image', 'like', '%wp-content%')
                    ->orWhere('gallery', 'like', '%wp-content%');
            })
            ->count();

        if ($dbCount > 0) {
            $issues[] = "{$dbCount} database record(s) still reference wp-content URLs.";
        }

        $seedFiles = [
            app_path('Data/PropertiesPageData.php'),
            app_path('Data/PropertyDetailData.php'),
        ];

        foreach ($seedFiles as $file) {
            if (is_file($file) && str_contains((string) file_get_contents($file), 'wp-content')) {
                $issues[] = basename($file).' still contains wp-content URLs.';
            }
        }

        return $issues;
    }

    private function databaseStatus(): string
    {
        $count = Property::query()
            ->where(function ($query): void {
                $query->where('image', 'like', '%wp-content%')
                    ->orWhere('gallery', 'like', '%wp-content%');
            })
            ->count();

        return $count === 0 ? 'OK' : "{$count} record(s) need attention";
    }

    private function seedFileStatus(): string
    {
        $files = [
            app_path('Data/PropertiesPageData.php'),
            app_path('Data/PropertyDetailData.php'),
        ];

        foreach ($files as $file) {
            if (is_file($file) && str_contains((string) file_get_contents($file), 'wp-content')) {
                return 'Needs sync';
            }
        }

        return 'OK';
    }

    private function siteAssetStatus(): string
    {
        $required = [
            config('site.logo'),
            config('site.favicon'),
            config('site.hero_video'),
            config('site.default_property_image'),
        ];

        foreach ($required as $path) {
            if (! is_file(public_path((string) $path))) {
                return 'Missing: '.basename((string) $path);
            }
        }

        return 'OK';
    }
}
