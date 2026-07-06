<?php

$path = __DIR__.'/../Properties-Export-2026-June-16-1029.csv';
$handle = fopen($path, 'r');
$headers = fgetcsv($handle);
$idx = array_flip($headers);

$stats = ['total' => 0, 'elementor_rera' => 0, 'content_rera' => 0, 'any_rera' => 0];
$samples = [];

while (($row = fgetcsv($handle)) !== false) {
    $stats['total']++;
    $slug = $row[$idx['Slug']] ?? '';
    $elementor = $row[$idx['_elementor_data']] ?? '';
    $content = $row[$idx['Content']] ?? '';

    $hasElementor = stripos($elementor, 'RERA ID') !== false || stripos($elementor, 'PR/GJ') !== false;
    $hasContent = stripos($content, 'RERA') !== false || stripos($content, 'PR/GJ') !== false;

    if ($hasElementor) {
        $stats['elementor_rera']++;
    }
    if ($hasContent) {
        $stats['content_rera']++;
    }
    if ($hasElementor || $hasContent) {
        $stats['any_rera']++;
        if (count($samples) < 3) {
            $samples[] = $slug;
        }
    }
}

fclose($handle);

print_r($stats);
echo 'Samples: '.implode(', ', $samples).PHP_EOL;
