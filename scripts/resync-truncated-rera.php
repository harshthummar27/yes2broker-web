<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sync = app(App\Services\PropertyReraSyncService::class);
$slugs = [
    'airan-shela-24', 'binori', 'captown-enhance', 'imperia-vista', 'lotus-developers',
    'samarth-h7', 'shlok-amaltas', 'sky-elegante', 'zircon-classic',
];

foreach ($slugs as $slug) {
    $property = App\Models\Property::query()->where('slug', $slug)->first();
    if ($property === null) {
        echo "Missing: $slug\n";
        continue;
    }
    $before = $property->overview['rera_id'] ?? null;
    $result = $sync->syncProperty($property);
    $after = $property->fresh()->overview['rera_id'] ?? null;
    echo "{$slug}: {$result['status']} | {$before} => {$after}\n";
    usleep(250000);
}
