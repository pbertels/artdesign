<?php

$BLOCKS = 9;

// BID INCREMENTS
$STEPS = [
    100000000 => 1000,
    10000 => 1000,
    5000 => 500,
    4200 => 300,
    3800 => 200,
    3200 => 300,
    2000 => 200,
    1000 => 100,
    250 => 50,
    240 => 10,
    100 => 20,
    50 => 10,
    0 => 50,
];
function thumbnail($image)
{
    $thumb = str_replace('images/', 'images/thumbnails/', $image);
    return file_exists($thumb) ? $thumb : $image;
}
function euro($org)
{
    $price = "{$org}";
    if (strlen($price) > 3) {
        $last = substr($price, -3, 3);
        $first = substr($price, 0, -3);
        $price = "{$first}.{$last}";
    }
    return is_numeric($org) ? "{$price} &euro;" : "{$org}";
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

function hearts($value, $size = 12, $font = '', $colour = '')
{
    $f = $font == '' ? $font : '-' . $font;
    $c = $colour == '' ? $colour : '-' . $colour;
    return str_replace('&hearts;', "<img src=\"/images/hearts{$f}{$c}.svg\" height=\"{$size}\"/>", $value);
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
            $value = preg_replace('/[\s]*\$\$\$[\$]*[\s]*/', "<br>", trim($value));
            $record[$header[$index]] = $value;
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

        // afwisseling creëren
        $record['sortkey'] = random_int(0, $BLOCKS) . strtoupper(substr($record['TYPE'], 0, 1) . substr($record['TYPE'], -4, 1));
        if ($code != '_VERKOOP') $art[$code] = $record;
    }
}
fclose($fp);

// SORTEREN
array_multisort(array_column($art, 'Lot'), SORT_ASC, array_column($art, 'sortkey'), SORT_ASC, array_column($art, 'Prijs'), SORT_ASC, $art);

// PROPER
// $lotnrs = array_column($art, 'Lot');
$lotnrs = [100];
foreach ($art as $code => $artwork) {
    $artwork['Prijs'] = $artwork['Prijs'] == 0 ? 'TO BE DECIDED' : start($artwork['Prijs']);
    $artwork['PrijsEuro'] = euro($artwork['Prijs']);
    $artwork['Instagram'] = str_replace('@', '', $artwork['Instagram']);
    $artwork['Biografie'] = $artwork['BioBewerkt'] == '' ? $artwork['BioOrigineel'] : $artwork['BioBewerkt'];
    $artwork['WerkHoofdletters'] = str_replace(['é', 'â'], ['E', 'A'], strtoupper($artwork['Werk']));
    $artwork['WerkHoofdlettersKorter'] = str_replace(['é', 'â'], ['E', 'A'], strtoupper($artwork['WerkKorter']));
    $artwork['ßß'] = str_replace(['é', 'â'], ['E', 'A'], strtoupper($artwork['WerkKorter']));
    $artwork['KunstDesignerAntonGreen'] = hearts($artwork['KunstDesigner'], 20, 'anton', 'green');
    $artwork['KunstDesignerAntonGreenSmaller'] = hearts(str_replace(' en ', ' <br>en ', $artwork['KunstDesigner']), 14, 'anton', 'green');
    $artwork['KunstDesignerAntonBlack'] = hearts($artwork['KunstDesigner'], 15, 'anton');
    $artwork['KunstDesignerAntonBlackSmaller'] = hearts($artwork['KunstDesigner'], 11, 'anton');
    $artwork['OverWerkelveticaBlack'] = hearts($artwork['OverWerk'], 7, '');
    $artwork['KunstDesignerHelveticaBlack'] = hearts($artwork['KunstDesigner'], 7, '');
    $artwork['BiografieHelveticaBlack'] = hearts($artwork['Biografie'], 7, '');
    $artwork['KunstDesignerShort'] = str_replace([' en Nathalie Sternotte'], [' en<br>Nathalie Sternotte'], $artwork['KunstDesigner']);
    if (is_array($artwork['im']) && count($artwork['im']) > 0) {
        $artwork['Afbeelding'] = str_replace('./', 'https://stgl.be/', $artwork['im'][array_key_first($artwork['im'])]);
        $artwork['AfbeeldingKlein'] = str_replace('./', 'https://stgl.be/', thumbnail($artwork['im'][array_key_first($artwork['im'])]));
    }
    // $artwork

    if ($artwork['Lot'] == '' || $artwork['Lot'] == 0) {
        $next = max($lotnrs) + 1;
        $artwork['Lot'] = $next;
    }
    if (in_array($artwork['Lot'], $lotnrs)) {
        $t = ((1 * substr($artwork['Lot'], 0, 1)) + 1) * 100;
        for ($i = 0; $i < count($lotnrs); $i++) {
            if ($lotnrs[$i] < $t) $next = $lotnrs[$i];
        }
        $next++;
        $artwork['Lot'] = $next;
    } else {
    }
    $lotnrs[] = $artwork['Lot'];
    $art[$code] = $artwork;
}

// SORTEREN
array_multisort(array_column($art, 'Lot'), SORT_ASC, $art);
