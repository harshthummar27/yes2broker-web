<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = App\Models\Property::where('slug', 'adarsh-aster-demo')->first();
echo 'brochure_url: '.$p->brochure_url.PHP_EOL;
echo 'resolved: '.$p->resolveBrochureUrl().PHP_EOL;
$path = storage_path('app/public/'.ltrim($p->brochure_url, '/'));
echo 'exists: '.(is_file($path) ? 'yes' : 'no').PHP_EOL;
echo 'path: '.$path.PHP_EOL;
