<?php

use ArtDesign\PdfCatalog;

require __DIR__ . '/vendor/autoload.php';

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
            $record[$header[$index]] = trim($value);
        }
        $code = $record['Code'];
        if ($code == '' || substr(strtoupper($code), 0, 5) == 'OPGEV') $code = strtolower($code) . count($art);
        $record['im'] = isset($images[$code]) ? $images[$code] : [];
        $art[$code] = $record;
    }
}
fclose($fp);

// // TEST
// echo "<pre>" . count($art) . print_r($art, true) . '</pre>';
// exit;


// DEFINITIONS
$RED = [235, 90, 60];
$GREEN = [80, 127, 35];
$BLACK = [0, 0, 0];

// CREATE PDF
$catalog = new PdfCatalog();
$catalog->setFont('anton', '', 10);
$catalog->AddPage();
// $catalog->Rect(0, 0, 216, 216, 'F', [], $DARK);
$catalog->setColorArray('text', $RED);
// $catalog->writeHTMLCell(190, 10, 13, 13, '<h1 style="color: rgb(235,90,56)">ART &amp; DESIGN</h1>', 'LTRB', 1, false, true, 'C', false);
$catalog->writeHTMLCell(190, 25, 13, 13, '<h1 style="font-size: 700%">ART &amp; DESIGN</h1>', 0, 1, false, true, 'C', false);
$catalog->setColorArray('text', $GREEN);
$catalog->writeHTMLCell(190, 25, 13, 38, '<h1 style="font-size: 645%">FOR PALESTINE</h1>', 0, 1, false, true, 'C', false);

// ART
/*
 [mgoossens1] => Array
        (
            [KunstDesigner] => Mario Goossens en Nathalie Sternotte
            [Schenker] => idem
            [BioBewerkt] => Dit boek is een samenwerking tussen Mario Goossens en Nathalie Sternotte.  Mario is een muzikaal multitalent, vooral bekend als drummer van Triggerfinger en als vaste waarde bij onder meer Bazart, Hooverphonic en Monza. Een Limburgse vijftiger die de wereld rondtrok en overal klank en ritme vond.  Nathalie is graficus met een frisse, eigenzinnige blik: ze zoekt geen standaardoplossingen. Haar creativiteit zit vaak in de uitvoering—precies, tactiel, doordacht—maar gaat moeiteloos hand in hand met sterke beeldvorming.
            [Werk] => Detailed drumlines
            [OverWerk] => Detailed drumlines is een creative benadering van een klassiek drumboek. Niet enkel de inhoud maar vooral de vormgeving telt. Elk nummer heeft zijn eigen verhaal, elk verhaal heeft zijn eigen visuele voorstelling. Hett is geen drumboek maar een doosje met waardepapieren. Aan jou om het esthetisch of praktisch te gebruiken.
            [BioOrigineel] => Dit boek is een samenwerking tussen Mario Goossens en Nathalie Sternotte. Mario is een muzikaal multitalent, vooral bekend als drummer van Triggerfinger en als vaste waarde bij onder meer Bazart, Hooverphonic en Monza. Een Limburgse vijftiger die de wereld rondtrok en overal klank en ritme vond. Nathalie is graficus met een frisse, eigenzinnige blik: ze zoekt geen standaardoplossingen. Haar creativiteit zit vaak in de uitvoering—precies, tactiel, doordacht—maar gaat moeiteloos hand in hand met sterke beeldvorming.
            [Prijs] => 50€
            [Formaat] => 250 mm breed x 350 mm hoog x 40 mm diep
            [Code] => mgoossens1
            [im] => Array
                (
                    [a] => ./images/mgoossens1a.jpg
                    [b] => ./images/mgoossens1b.jpg
                )

        )

*/

if (true) {
    foreach ($art as $code => $artwork) {
        $catalog->AddPage();
        $catalog->setFont('anton', '', 10);
        $work = strtoupper($artwork['Werk']);
        $artist = $artwork['KunstDesigner'];
        $catalog->setColorArray('text', $RED);
        $catalog->writeHTML("<h1 style=\"font-size: 250%\">{$work}</h1>");
        $catalog->setColorArray('text', $GREEN);
        $catalog->writeHTML("<h1>{$artist}</h1>");
        $catalog->setColorArray('text', $BLACK);
        $catalog->setFont('helvetica', '', 10);
        if ($artwork['Schenker'] != 'idem') $catalog->writeHTML("<p>geschonken door {$artwork['Schenker']}</p>");

        $catalog->setFont('anton', '', 10);
        $catalog->writeHTML("<p></p><h3>Over het werk</h3>");
        $catalog->setFont('helvetica', '', 10);
        $catalog->writeHTML("<p>{$artwork['OverWerk']}</p><p></p>");

        $catalog->setFont('anton', '', 10);
        $catalog->writeHTML("<h3>Biografie {$artist}</h3>");
        $bio = $artwork['BioBewerkt'] == '' ? $artwork['BioOrigineel'] : $artwork['BioBewerkt'];
        $catalog->setFont('helvetica', '', 10);
        $catalog->writeHTML("<p>{$bio}</p>");

        $catalog->AddPage();
        if (is_array($artwork['im'])) {
            $count = count($artwork['im']);
            // echo "<pre>{$artist} - {$count}</pre>";
            if ($count > 0) {
                foreach ($artwork['im'] as $pic => $image) {
                    $catalog->Image($image, 25, 25, 166, 166, '', '', '', true, 600, 'C', false, false, 0, 'CM', false, false, false);
                    // $catalog->Image($image, 0, 0, 216, 216, '', '', '', true, 600, 'C', false, false, 0, true, false, true, false);
                }
            } else {
                $catalog->Rect(50, 50, 116, 116, 'F', [], substr($code, 0, 5) == 'opgev' ? $GREEN : $RED);
            }
        }
    }
}

// OUTPUT
$timestamp = date('Ymd-Hi');
$catalog->output("catalog-{$timestamp}.pdf");
