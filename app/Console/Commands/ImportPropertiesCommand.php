<?php

namespace App\Console\Commands;

use App\Models\Property;
use Database\Seeders\PropertySeeder;
use Illuminate\Console\Command;

class ImportPropertiesCommand extends Command
{
    protected $signature = 'properties:import {--fresh : Delete all properties before import}';

    protected $description = 'Import properties from seed data into the database';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $deleted = Property::query()->count();
            Property::query()->delete();
            $this->warn("Deleted {$deleted} existing properties.");
        }

        $before = Property::query()->count();

        $this->info('Importing properties into database...');
        $this->call('db:seed', ['--class' => PropertySeeder::class, '--force' => true]);

        $after = Property::query()->count();
        $imported = $after - ($this->option('fresh') ? 0 : $before);

        $this->newLine();
        $this->info("Done! Total properties in database: {$after}");

        if ($imported > 0) {
            $this->line("Imported/updated: {$imported}");
        }

        return self::SUCCESS;
    }
}
