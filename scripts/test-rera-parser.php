<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$parser = app(App\Services\PropertyPageHtmlParser::class);
$html = file_get_contents('https://yes2broker.in/108-yards/');
$overview = $parser->extractOverview($html);
echo 'Overview rera: '.($overview['rera_id'] ?? 'none')."\n";
echo 'Extract: '.$parser->extractReraId($html)."\n";
