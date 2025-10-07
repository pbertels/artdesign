<?php

require __DIR__ . '/parse.php';

$cols = isset($_GET['bio']) ? ['Code', 'Lot', 'Code', 'Werk'] : ['Lot', 'KunstDesigner', 'Werk', 'Prijs', 'Afbeelding', 'AfbeeldingKlein'];

$timestamp = date('Ymd-Hi');
$filename =  isset($_GET['bio']) ? "proper-{$timestamp}.tsv" : "proper.tsv";

header("Content-Type: text/csv");
header("Content-Disposition: attachment;filename={$filename}");

foreach ($cols as $col) {
    echo "$col\t";
}
echo "\n";
foreach ($art as $code => $artwork) {
    foreach ($cols as $col) {
        $value = $artwork[$col];
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = str_replace('<br>', '', $value);
        str_replace(';', '', $value);
        echo "{$value}\t";
    }
    echo "\n";
}
