<?php

// BID INCREMENTS
$STEPS = [
    100000000 => 1000,
    10000 => 1000,
    2000 => 500,
    1000 => 250,
    500 => 100,
    200 => 50,
    60 => 20,
    0 => 10,
];
function euro($price) {
    $price = "{$price}";
    if (strlen($price) > 3) {
        $last = substr($price, -3, 3);
        $first = substr($price, 0, -3);
        $price = "{$first}.{$last}";
    }
    return "{$price} &euro;";
}
function increment($bod)
{
    global $STEPS;
    $prev = max(array_keys($STEPS));
    foreach ($STEPS as $s => $inc) {
        if ($bod >= $s) {
            $base = $s;
            $increment = $inc;
            break;
        }
        $prev = $s;
    }
    return $bod + $increment > $prev ? $prev : $bod + $increment;
}
function start($bod)
{
    global $STEPS;
    if (!is_numeric($bod)) $bod = 0;
    foreach ($STEPS as $s => $inc) {
        if ($bod >= $s) {
            $base = ceil($bod / $inc) * $inc;
            if ($base < $inc) $base = $inc;
            return $base;
        }
    }
}

// FIND IMAGES
$images = [];
foreach (glob('./images/*.jpg') as $filename) {
    $name = substr($filename, 9, -4);
    $num = substr($name, -2, 1);
    if (is_numeric($num)) {
        $code = substr($name, 0, -1);
        $pic = substr($name, -1, 1);
    } else {
        $code = $name;
        $pic = 'u';
    }
    $temp = substr($name, 0, 1) == '_';
    if ($temp) {
        $pic = 'TEMP';
    }
    if (!isset($images[$code])) $images[$code] = [];
    $images[$code][$pic] = $filename;
}

// PARSE DATA
$art = [];
$header = [];
$fp = fopen('./data.tsv', 'r');
while (!feof($fp)) {
    $line = fgets($fp, 20048);
    $data = str_getcsv($line, "\t");
    if (count($header) == 0) {
        $header = $data;
    } else {
        foreach ($data as $index => $value) {
            $record[$header[$index]] = preg_replace('/[\s]*\$\$\$[\$]*[\s]*/', "<br>", trim($value));
        }
        $code = $record['Code'];
        if ($code == '' || substr(strtoupper($code), 0, 5) == 'OPGEV') $code = strtolower($code) . count($art);
        if (isset($images[$code])) {
            $record['im'] = $images[$code];
            $record['foto'] = 1;
        } else {
            $record['im'] = [];
            $record['foto'] = 0;
        }
        if ($record['Lot'] == '') $record['Lot'] = '999 TBD';
        $record['Prijs'] = start($record['Prijs']);
        if ($code != '_VERKOOP') $art[$code] = $record;
    }
}
fclose($fp);

// SORTEREN
array_multisort(array_column($art, 'Lot'), SORT_ASC, array_column($art, 'TYPE'), SORT_ASC, array_column($art, 'Prijs'), SORT_ASC, array_column($art, 'KunstDesigner'), SORT_ASC, $art);
