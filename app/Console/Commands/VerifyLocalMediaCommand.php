<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

class VerifyLocalMediaCommand extends Command
{
    protected $signature = 'media:verify-local';

    protected $description = 'Check for remaining WordPress CDN dependencies';

    public function handle(): int
    {
        $dbIssues = Property::query()
            ->where(function ($query): void {
                $query->where('image', 'like', '%wp-content%')
                    ->orWhere('gallery', 'like', '%wp-content%');
            })
            ->orderBy('slug')
            ->get(['slug', 'image']);

        $seedIssues = [];

        foreach ([
            'PropertiesPageData.php' => app_path('Data/PropertiesPageData.php'),
            'PropertyDetailData.php' => app_path('Data/PropertyDetailData.php'),
        ] as $label => $path) {
            if (is_file($path) && str_contains((string) file_get_contents($path), 'wp-content')) {
                $count = substr_count((string) file_get_contents($path), 'wp-content');
                $seedIssues[] = "{$label} ({$count} references)";
            }
        }

        if ($dbIssues->isEmpty() && $seedIssues === []) {
            $this->info('All clear — no wp-content dependencies in database or seed files.');

            return self::SUCCESS;
        }

        if ($dbIssues->isNotEmpty()) {
            $this->error('Database records with WordPress URLs:');
            foreach ($dbIssues as $property) {
                $this->line("  - {$property->slug}: {$property->image}");
            }
        }

        if ($seedIssues !== []) {
            $this->error('Seed files with WordPress URLs:');
            foreach ($seedIssues as $issue) {
                $this->line("  - {$issue}");
            }
        }

        $this->newLine();
        $this->line('Run <comment>php artisan wordpress:decouple</comment> to fix remaining issues.');

        return self::FAILURE;
    }
}
