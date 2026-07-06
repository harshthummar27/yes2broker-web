<?php

$url = 'https://yes2broker.in/properties-details/108-yards/';
$html = file_get_contents($url, false, stream_context_create([
    'http' => ['timeout' => 15, 'header' => "User-Agent: Mozilla/5.0\r\n"],
]));

if ($html === false) {
    exit("fetch failed\n");
}

echo 'Length: '.strlen($html)."\n";
echo 'Has Overview: '.(stripos($html, 'Overview') !== false ? 'yes' : 'no')."\n";
echo 'Has RERA: '.(stripos($html, 'RERA') !== false ? 'yes' : 'no')."\n";
echo 'Has PR/GJ: '.(stripos($html, 'PR/GJ') !== false ? 'yes' : 'no')."\n";

if (preg_match('/RERA.{0,200}/is', $html, $m)) {
    echo "Snippet: ".$m[0]."\n";
}

if (preg_match('/Overview.{0,500}/is', $html, $m)) {
    echo "Overview snippet: ".html_entity_decode(strip_tags($m[0]))."\n";
}
