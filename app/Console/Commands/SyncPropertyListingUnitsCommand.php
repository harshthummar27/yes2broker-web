<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PropertyListingUnitService;
use Illuminate\Console\Command;

class SyncPropertyListingUnitsCommand extends Command
{
    protected $signature = 'properties:sync-listing-units';

    protected $description = 'Sync property listing unit rows from overview JSON into property_listing_units table';

    public function handle(PropertyListingUnitService $service): int
    {
        $result = $service->syncAllFromOverviewJson();

        $this->info("Synced {$result['synced']} properties. Skipped {$result['skipped']} without unit configurations.");

        return self::SUCCESS;
    }
}
