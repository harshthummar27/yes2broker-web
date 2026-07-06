<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\PropertyPageHtmlParser;

$parser = app(PropertyPageHtmlParser::class);
$slugs = ['108-yards', 'anand-paramount', '24-karat'];

foreach ($slugs as $slug) {
    foreach ([
        "https://yes2broker.in/properties-details/{$slug}/",
        "https://yes2broker.in/{$slug}/",
        "https://yes2broker.in/property/{$slug}/",
    ] as $url) {
        $html = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 15, 'header' => "User-Agent: Yes2Broker-RERA-Sync/1.0\r\n"],
        ]));
        if ($html === false) {
            echo "$url => FAIL\n";
            continue;
        }
        $overview = $parser->extractOverview($html);
        $rera = $overview['rera_id'] ?? '(none)';
        echo "$url => RERA: ".substr($rera, 0, 80)."\n";
        break;
    }
}
