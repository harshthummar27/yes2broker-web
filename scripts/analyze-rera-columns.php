<?php

$path = __DIR__.'/../Properties-Export-2026-June-16-1029.csv';
$handle = fopen($path, 'r');
$headers = fgetcsv($handle);
$idx = array_flip($headers);

$columnHits = [];

while (($row = fgetcsv($handle)) !== false) {
    foreach ($headers as $i => $header) {
        $val = $row[$i] ?? '';
        if ($val !== '' && (stripos($val, 'PR/GJ') !== false || preg_match('/RERA\s*ID/i', $val))) {
            $columnHits[$header] = ($columnHits[$header] ?? 0) + 1;
        }
    }
}

fclose($handle);

arsort($columnHits);
print_r($columnHits);
