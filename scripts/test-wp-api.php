<?php

$endpoints = [
    'https://yes2broker.in/wp-json/wp/v2/properties-details/17908',
    'https://yes2broker.in/wp-json/wp/v2/properties-details?slug=108-yards',
];

foreach ($endpoints as $url) {
    echo "=== $url ===\n";
    $json = @file_get_contents($url);
    if ($json === false) {
        echo "FAIL\n\n";
        continue;
    }
    echo (stripos($json, 'RERA') !== false || stripos($json, 'PR/GJ') !== false ? 'HAS RERA' : 'no rera')."\n";
    echo 'len '.strlen($json)."\n\n";
}
