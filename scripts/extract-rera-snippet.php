<?php

$html = file_get_contents('https://yes2broker.in/108-yards/');
file_put_contents(__DIR__.'/sample-root-slug.html', $html);
if (preg_match('/RERA.{0,300}/is', $html, $m)) {
    echo html_entity_decode(strip_tags($m[0]))."\n";
}
if (preg_match('/PR\/GJ[^<"\']+/i', $html, $m)) {
    echo "ID: ".$m[0]."\n";
}
