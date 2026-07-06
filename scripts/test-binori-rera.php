<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$html = file_get_contents('https://yes2broker.in/binori/');
file_put_contents(__DIR__.'/binori.html', $html);

$parser = app(App\Services\PropertyPageHtmlParser::class);

echo "Overview:\n";
print_r($parser->extractOverview($html));
echo "RERA: ".$parser->extractReraId($html)."\n";

preg_match_all('/PR\/GJ[^<"\']+/i', $html, $m);
echo "All PR/GJ matches: ".count($m[0])."\n";
foreach (array_slice($m[0], 0, 8) as $match) {
    echo "  - ".substr(html_entity_decode($match), 0, 100)."\n";
}
