<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\PropertyPageHtmlParser;

$parser = app(PropertyPageHtmlParser::class);
$slug = '108-yards';
$url = "https://yes2broker.in/{$slug}/";
$html = file_get_contents($url, false, stream_context_create([
    'http' => ['timeout' => 20, 'header' => "User-Agent: Mozilla/5.0\r\n"],
]));

if ($html === false) {
    exit("fail\n");
}

echo "URL: $url\n";
echo 'len: '.strlen($html)."\n";
echo 'RERA: '.(stripos($html, 'RERA') !== false ? 'yes' : 'no')."\n";
echo 'Overview: '.(stripos($html, 'Overview') !== false ? 'yes' : 'no')."\n";

$overview = $parser->extractOverview($html);
print_r($overview);
