<?php

$path = __DIR__.'/../Properties-Export-2026-June-16-1029.csv';
$handle = fopen($path, 'r');
$headers = fgetcsv($handle);
$idx = array_flip($headers);

$elementorFilled = 0;
$contentLong = 0;

while (($row = fgetcsv($handle)) !== false) {
    $ed = trim($row[$idx['_elementor_data']] ?? '');
    $content = $row[$idx['Content']] ?? '';
    if ($ed !== '') {
        $elementorFilled++;
    }
    if (strlen($content) > 500) {
        $contentLong++;
    }
}

fclose($handle);

echo "elementor_data filled: $elementorFilled\n";
echo "content > 500 chars: $contentLong\n";

// sample elementor length for first row
$handle = fopen($path, 'r');
$headers = fgetcsv($handle);
$row = fgetcsv($handle);
$idx = array_flip($headers);
echo 'First elementor len: '.strlen($row[$idx['_elementor_data']] ?? '')."\n";
echo 'First content len: '.strlen($row[$idx['Content']] ?? '')."\n";
