<?php

use App\Models\Property;
use App\Services\PropertyStorageSyncService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('properties:sync-prices', function () {
    $count = 0;

    Property::query()->each(function (Property $property) use (&$count): void {
        $property->price_min_lakhs = Property::parsePriceMinLakhs($property->price);
        $property->saveQuietly();
        $count++;
    });

    $this->info("Synced price_min_lakhs for {$count} properties.");
})->purpose('Recalculate price_min_lakhs from price text for budget search');

Artisan::command('properties:sync-images', function (PropertyStorageSyncService $sync) {
    $this->info('Syncing property images from storage/app/public/properties into the database...');

    $stats = $sync->syncFromStorage();

    $this->info("Updated: {$stats['updated']}");
    $this->info("Unchanged: {$stats['unchanged']}");
    $this->info("Missing folder: {$stats['missing_folder']}");
    $this->info("Empty folder: {$stats['empty_folder']}");
})->purpose('Push images from public/storage/properties into property image and gallery fields');
