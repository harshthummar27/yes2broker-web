<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$truncated = [];
$onRequest = 0;

foreach (App\Models\Property::query()->cursor() as $property) {
    $rera = $property->overview['rera_id'] ?? null;
    if (blank($rera)) {
        continue;
    }
    if (str_contains(strtolower($rera), 'available on request')) {
        $onRequest++;
        continue;
    }
    // Full Gujarat RERA IDs usually contain AUDA, AMC, Municipal, or a registration code segment
    if (! preg_match('/\/(MAA|RAA|CAA|MN)[A-Z0-9]/i', $rera) && strlen($rera) < 40) {
        $truncated[] = ['slug' => $property->slug, 'rera' => $rera];
    }
}

echo "On request: $onRequest\n";
echo "Likely truncated: ".count($truncated)."\n";
foreach (array_slice($truncated, 0, 20) as $row) {
    echo "  {$row['slug']}: {$row['rera']}\n";
}
