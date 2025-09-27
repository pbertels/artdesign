<?php

require __DIR__ . '/parse.php';

$cols = ['Lot', 'KunstDesigner', 'WerkHoofdletters', 'Prijs'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=proper.tsv');

foreach ($cols as $col) {
    echo "$col; ";
}
echo "\n";
foreach ($art as $code => $artwork) {
    foreach ($cols as $col) {
        $value = $artwork[$col];
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        str_replace(';', '', $value);
        echo "{$value}; ";
    }
    echo "\n";
}
