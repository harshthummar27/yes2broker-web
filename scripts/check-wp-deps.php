<?php

declare(strict_types=1);
use App\Models\Property;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

foreach (Property::query()->orderBy('slug')->get() as $property) {
    $gallery = json_encode($property->gallery ?? []);

    if (
        str_contains((string) $property->image, 'wp-content')
        || str_contains((string) $gallery, 'wp-content')
    ) {
        echo $property->slug.' | image='.$property->image.PHP_EOL;
        echo '  gallery sample: '.substr((string) $gallery, 0, 200).PHP_EOL;
    }
}
