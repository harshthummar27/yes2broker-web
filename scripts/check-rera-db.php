<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Property;

$total = Property::count();
$withRera = 0;
$availableOnRequest = 0;
$realRera = 0;

foreach (Property::query()->get(['slug', 'overview']) as $p) {
    $rera = $p->overview['rera_id'] ?? null;
    if (blank($rera)) {
        continue;
    }
    $withRera++;
    if (stripos($rera, 'request') !== false) {
        $availableOnRequest++;
    } elseif (stripos($rera, 'PR/GJ') !== false || stripos($rera, 'RERA') !== false) {
        $realRera++;
    }
}

echo "Total: $total\n";
echo "With rera_id set: $withRera\n";
echo "Available on request: $availableOnRequest\n";
echo "Real RERA IDs: $realRera\n";
