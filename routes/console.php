<?php

use App\Models\Property;
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
