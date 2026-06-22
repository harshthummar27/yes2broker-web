<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\MediaMigrationService;
use Illuminate\Console\Command;

class MigrateWordPressMediaCommand extends Command
{
    protected $signature = 'media:migrate-wordpress
        {--site : Download branding, partner, and bank assets to public/}
        {--properties : Download property images to storage/app/public}
        {--property= : Migrate a single property slug}
        {--dry-run : Preview without writing files or updating the database}';

    protected $description = 'Migrate WordPress CDN images to local storage';

    public function handle(MediaMigrationService $migration): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $runSite = (bool) $this->option('site');
        $runProperties = (bool) $this->option('properties');
        $propertySlug = $this->option('property');

        if (! $runSite && ! $runProperties && $propertySlug === null) {
            $runSite = true;
            $runProperties = true;
        }

        if ($propertySlug !== null) {
            $runProperties = true;
        }

        if ($dryRun) {
            $this->warn('Dry run mode — no files or database records will be changed.');
        }

        if ($runSite) {
            $this->importSiteAssets($migration, $dryRun);
        }

        if ($runProperties) {
            $this->migrateProperties($migration, $propertySlug, $dryRun);
        }

        if (! $dryRun && ($runSite || $runProperties)) {
            $this->newLine();
            $this->callSilent('storage:link');
            $this->info('Storage link verified.');
        }

        return self::SUCCESS;
    }

    private function importSiteAssets(MediaMigrationService $migration, bool $dryRun): void
    {
        $this->info('Importing site branding and shared media...');
        $stats = $migration->importSiteAssets($dryRun);
        $this->reportStats($stats);

        if (! $dryRun) {
            $this->line('Site assets are served from <comment>public/images</comment> and <comment>public/videos</comment>.');
        }
    }

    private function migrateProperties(MediaMigrationService $migration, ?string $propertySlug, bool $dryRun): void
    {
        $this->info('Migrating property images...');
        $stats = $migration->migrateProperties($propertySlug, $dryRun);
        $this->line("Properties processed: <info>{$stats['properties']}</info>");
        $this->reportStats($stats);

        if (! $dryRun) {
            $this->line('Property images are served from <comment>/storage/properties/...</comment>.');
        }
    }

    /**
     * @param  array{downloaded?: int, skipped?: int, failed?: int, errors?: list<string>}  $stats
     */
    private function reportStats(array $stats): void
    {
        $this->line("Downloaded: <info>{$stats['downloaded']}</info>");
        $this->line("Skipped: <comment>{$stats['skipped']}</comment>");
        $this->line("Failed: <error>{$stats['failed']}</error>");

        foreach ($stats['errors'] ?? [] as $error) {
            $this->line("  - {$error}");
        }
    }
}
