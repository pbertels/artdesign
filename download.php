<?php

ini_set("memory_limit", -1);
use ArtDesign\PdfCatalog;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/sponsors.php';

// TYPE
$TYPE = isset($_GET['type']) && in_array($_GET['type'], ['binnenwerk', 'kaft']) ? $_GET['type'] : 'binnenwerk';

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

// SORTEREN
array_multisort(array_column($art, 'TYPE'), SORT_ASC, array_column($art, 'Prijs'), SORT_ASC, array_column($art, 'KunstDesigner'), SORT_ASC, $art);
// echo '<pre>' . print_r($art, true) . '</pre>';
// exit;


// DEFINITIONS
$RED = [235, 90, 60];
$GREEN = [80, 127, 35];
$BLACK = [0, 0, 0];
$WHITE = [255, 255, 255];
$BLEED = 2;
$SIZE = 210 + 2 * $BLEED;
$MARGIN = 15;
$GUTTER = 30;
$WIDTH = $SIZE - 2 * $MARGIN - $GUTTER;
$leftODD = $MARGIN + $GUTTER;
$leftEVEN = $MARGIN;
$SPACER = 10;
$timestamp = date('Ymd-Hi');

// CREATE PDF of RIGHT TYPE
if ($TYPE != '') {
    $catalog = new PdfCatalog($SIZE, $SIZE);
    $catalog->SetMargins($MARGIN, $MARGIN, null, true);
    $catalog->SetBooklet(true, $MARGIN, $MARGIN + $GUTTER);
    // $catalog->setFont('anton', '', 10);
    // $catalog->AddPage();
    // $catalog->setColorArray('text', $RED);
    // $catalog->writeHTMLCell($WIDTH, 25, $leftODD, $MARGIN, '<h1 style="font-size: 700%">ART &amp; DESIGN</h1>', 0, 1, false, true, 'C', false);
    // $catalog->setColorArray('text', $GREEN);
    // $catalog->writeHTMLCell($WIDTH, 25, $leftODD, $MARGIN + 25, '<h1 style="font-size: 645%">FOR PALESTINE</h1>', 0, 1, false, true, 'C', false);

    // PREFACE
    $afronden = ((count($art)-count($art)%10)/10);
    $cijfers = ['nul', 'tien', 'twintig', 'dertig', 'veertig', 'vijftig', 'zestig', 'zeventig', 'tachtig', 'negentig', 'honderd', 'honderdentien'];
    $AANTAL = $cijfers[$afronden];
    $catalog->AddSectionPage('Voorwoord', $RED, $WHITE, $WIDTH, $leftODD);
    $catalog->AddSectionPage('', $RED, $WHITE, $WIDTH, $leftODD);
    $catalog->AddPage();
    $catalog->setFont('anton', '', 10);
    $catalog->setColorArray('text', $RED);
    $catalog->writeHTMLCell($WIDTH, 25, $leftODD, $MARGIN, '<h1 style="font-size: 500%">ART &amp; DESIGN</h1>', 0, 1, false, true, 'L', false);
    $catalog->setColorArray('text', $GREEN);
    $catalog->writeHTMLCell($WIDTH, 25, $leftODD, $MARGIN + 18, '<h1 style="font-size: 450%">FOR PALESTINE</h1>', 0, 1, false, true, 'L', false);
    $catalog->setColorArray('text', $BLACK);
    $catalog->setFont('helvetica', '', 11);
    $catalog->writeHTMLCell($WIDTH, 25, $leftODD, $MARGIN + 40, "
<p></p>
<p>Meer dan {$AANTAL} kunstenaars en designers schonken hun werk voor deze veiling: schilderijen, beelden en designobjecten die samen een uniek en divers geheel vormen. Elk stuk is niet alleen een uitdrukking van creativiteit, maar ook van solidariteit.</p>
<p>De opbrengst gaat integraal naar het Rode Kruis, Oxfam en UNWRA. Drie organisaties die dagelijks verschil maken, en die we met dit initiatief extra willen ondersteunen. Uw aanwezigheid en biedingen zorgen ervoor dat kunst hier méér wordt dan bewondering alleen: ze wordt een daad van verbondenheid.</p>
<p>Onze dank gaat ook uit naar onze sponsors: {$SPONSORLIST}. Dankzij hun steun kunnen we dit evenement niet alleen mogelijk maken, maar ook aangenaam, feestelijk en net iets minder dorstig.</p>
<p>Blader gerust, kies met uw hart, en laat u meeslepen door de energie van de veiling. Want uiteindelijk wint niet enkel de hoogste bieder, maar vooral de mensen en doelen die we samen een stap vooruit helpen.</p>
<p></p>
<p>Waar kunst en solidariteit elkaar raken, ontstaat hoop.</p>
<p></p>
<p>Met warmte en dankbaarheid,</p>
<p>Fred, Evelyn, Peter, Pieter, Dotje & Jasmien</p>
");

    // ART
    $prev = 'brol';
    $section = 0;
    foreach ($art as $code => $artwork) {
        $t = $artwork['TYPE'] != '' ? str_replace(['KLIEF', '...'], ['K- LIEF', ''], strtoupper($artwork['TYPE'])) : 'TE BEKIJKEN...';
        if ($t != $prev) {
            $section++;
            $catalog->AddSectionPage('', $section % 2 == 1 ? $GREEN : $RED, $WHITE, $WIDTH, $leftODD);
            $catalog->AddSectionPage($t, $section % 2 == 1 ? $GREEN : $RED, $WHITE, $WIDTH, $leftODD);
            $prev = $t;
        }
        $catalog->AddPage();
        $catalog->setFont('anton', '', 10);
        $work = strtoupper($artwork['Werk']);
        $artist = $artwork['KunstDesigner'];
        $catalog->setColorArray('text', $RED);
        $catalog->writeHTML("<h1 style=\"font-size: 250%\">{$work}</h1>");
        $catalog->setColorArray('text', $GREEN);
        $catalog->writeHTML("<h1>{$artist}</h1>");
        $catalog->setColorArray('text', $BLACK);
        $catalog->setFont('helvetica', '', 11);
        if ($artwork['Schenker'] != 'idem') $catalog->writeHTML("<p>geschonken door {$artwork['Schenker']}</p>");

        $catalog->setFont('anton', '', 10);
        $catalog->writeHTML("<p></p><h3>Over het werk</h3>");
        $catalog->setFont('helvetica', '', 11);
        $catalog->writeHTML("<p>{$artwork['OverWerk']}</p><p></p>");

        $catalog->setFont('anton', '', 10);
        $catalog->writeHTML("<h3>Biografie {$artist}</h3>");
        $bio = $artwork['BioBewerkt'] == '' ? $artwork['BioOrigineel'] : $artwork['BioBewerkt'];
        $catalog->setFont('helvetica', '', 11);
        $catalog->writeHTML("<p>{$bio}</p>");

        $catalog->AddPage();
        if (is_array($artwork['im'])) {
            $count = count($artwork['im']);
            if ($count > 0) {
                $x = $leftODD;
                $width = ($WIDTH - ($count - 1) * $SPACER) / $count;
                foreach ($artwork['im'] as $pic => $image) {
                    $catalog->Image($image, $x, $MARGIN, $width, 0); //, '', '', '', true, 600, 'C', false, false, 0, 'CM', false, false, false);
                    $x += $width + $SPACER;
                }
            } else {
                $catalog->Rect($leftODD, $MARGIN, $WIDTH, $WIDTH, 'F', [], substr($code, 0, 5) == 'opgev' ? $GREEN : $RED);
            }
        }
    }

    // SUMMARY
    $catalog->AddSectionPage('', $RED, $WHITE, $WIDTH, $leftODD);
    $catalog->AddSectionPage('Overzicht van alle loten', $RED, $WHITE, $WIDTH, $leftODD);
    $table = [];
    $i = 1;
    foreach ($art as $code => $artwork) {
        $artist = $artwork['KunstDesigner'];
        $work = $artwork['Werk'];
        $price = preg_replace('/[^0-9]/', '', $artwork['Prijs']);
        $number = substr('0000' . ($i++), -3, 3);
        $row = '<tr>';
        $row .= "<td width=\"2cm\" style=\"text-align: left\">{$number}</td>";
        $row .= "<td width=\"6cm\" style=\"text-align: left\">{$artist}</td>";
        $row .= "<td width=\"7cm\" style=\"text-align: left\">{$work}</td>";
        $row .= "<td width=\"2.5cm\" style=\"text-align: right\">&euro; {$price}</td>";
        $row .= '</tr>';
        $table[] = $row;
    }
    $header = '<tr style="font-weight: bold">';
    $header .= "<td width=\"2cm\" style=\"text-align: left\">Nummer</td>";
    $header .= "<td width=\"6cm\" style=\"text-align: left\">Kunstenaar / ontwerper</td>";
    $header .= "<td width=\"7cm\" style=\"text-align: left\">Titel of omschrijving van het item</td>";
    $header .= "<td width=\"2.5cm\" style=\"text-align: right\">Prijs</td>";
    $header .= '</tr>';
    $catalog->setColorArray('text', $BLACK);
    $step = 33;
    for ($i = 0; $i < count($table); $i += $step) {
        $catalog->AddPage();
        $catalog->setFont('anton', '', 11);
        $html = '<table>' . $header . '</table>';
        $catalog->writeHTML($html);
        $catalog->setFont('helvetica', '', 11);
        $html = '<table>' . implode('', array_slice($table, $i, $step)) . '</table>';
        $catalog->writeHTML($html);
    }

    // EXTRA PAGES
    while ($catalog->getNumPages() % 4 != 0) {
        $catalog->AddSectionPage('', $GREEN, $WHITE, $WIDTH, $leftODD);
    }
}

if ($TYPE == 'binnenwerk') {
    $catalog->output("catalog-{$timestamp}.pdf");
} else if ($TYPE == 'kaft') {
    $THICKNESS = (round(((2 * 0.48) + ($catalog->getNumPages() / 2 * 0.20)) / 2) + 1) * 0.5;
    $COVER = 2 * $SIZE + $THICKNESS - 2 * $BLEED;
    $kaft = new PdfCatalog($COVER, $SIZE);
    $kaft->setFont('anton', '', 10);

    $kaft->AddPage();
    $kaft->setColorArray('text', $RED);
    $kaft->writeHTMLCell($WIDTH, 25, $SIZE + $THICKNESS + $leftODD, $MARGIN, '<h1 style="font-size: 700%">ART &amp; DESIGN</h1>', 0, 1, false, true, 'C', false);
    $kaft->setColorArray('text', $GREEN);
    $kaft->writeHTMLCell($WIDTH, 25, $SIZE + $THICKNESS + $leftODD, $MARGIN + 25, '<h1 style="font-size: 645%">FOR PALESTINE</h1>', 0, 1, false, true, 'C', false);
    $kaft->setColorArray('text', $BLACK);
    $kaft->setFont('helvetica', '', 18);
    $kaft->writeHTMLCell($WIDTH, 25, $SIZE + $THICKNESS + $leftODD, $SIZE / 2, "<p>{$COVER} mm x {$SIZE} mm</p>", 0, 1, false, true, 'C', false);

    $kaft->Rect($SIZE, 0, $THICKNESS, $SIZE, 'F', [], $BLACK);

    $kaft->AddPage();
    $kaft->Rect(0, 0, $SIZE - 4/*+ $THICKNESS / 2*/, $SIZE, 'F', [], $RED);
    $kaft->Rect($SIZE + $THICKNESS + 4, 0, $SIZE - 4, $SIZE, 'F', [], $GREEN);

    $kaft->output("cover-{$timestamp}.pdf");
}
