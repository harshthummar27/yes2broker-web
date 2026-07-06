<?php

$url = 'https://yes2broker.in/properties-details/108-yards/';
$html = file_get_contents($url, false, stream_context_create([
    'http' => ['timeout' => 15, 'header' => "User-Agent: Mozilla/5.0\r\n"],
]));

file_put_contents(__DIR__.'/sample-live.html', $html);
echo 'saved '.strlen($html)." bytes\n";
echo (str_contains($html, 'Laravel') ? 'Laravel detected' : 'no Laravel')."\n";
echo (str_contains($html, 'yes2broker') ? 'yes2broker yes' : '')."\n";
echo (str_contains($html, 'Additional Amenities') ? 'Amenities section yes' : 'no amenities')."\n";
echo (str_contains($html, 'Descriptions') ? 'Descriptions yes' : 'no descriptions')."\n";
