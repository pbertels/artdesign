<?php

ini_set("memory_limit", -1);

use ArtDesign\PdfCatalog;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/sponsors.php';
require __DIR__ . '/parse.php';

// TYPE
$TYPE = isset($_GET['type']) && in_array($_GET['type'], ['binnenwerk', 'kaft']) ? $_GET['type'] : 'alles';
$COMPRESS = $TYPE == 'alles' ? true : isset($_GET['compress']);

// DEFINITIONS
$RED = [235, 90, 60];
$GREEN = [80, 127, 35];
$BLUE = [0, 0, 255];
$BLACK = [0, 0, 0];
$WHITE = [255, 255, 255];
$BLEED = 2;
$SIZE_W = 210 + 2 * $BLEED;
$SIZE_H = 148 + 2 * $BLEED;
$MARGIN = 10 + $BLEED;
$GUTTER = 10;
$WIDTH = $SIZE_W - 2 * $MARGIN - $GUTTER;
$HEIGHT = $SIZE_H - 2 * $MARGIN;
$leftODD = $MARGIN + $GUTTER;
$leftEVEN = $MARGIN;
$SPACER = 10;
$COL_ORIGINAL = 90;
$timestamp = date('Ymd-Hi');

function coverFront($kaft, $w, $h, $m, $l, $page = 0)
{
    $RED = [235, 90, 60];
    $GREEN = [80, 127, 35];
    $BLACK = [0, 0, 0];
    $WHITE = [255, 255, 255];

    $kaft->AddPage();
    $kaft->setFillColorArray([167, 219, 208]);
    $kaft->Rect(0, 35.5, $l + $w + $m, $h, 'F');
    $kaft->Image('./images/verf-transparant.png', $l, 30, $w);
    $kaft->setColorArray('text', $RED);
    $kaft->writeHTMLCell($w, 25, $l, $m, '<h1 style="font-size: 600%">ART &amp; DESIGN</h1>', 0, 1, false, true, 'C', false);
    $kaft->setColorArray('text', $GREEN);
    $kaft->writeHTMLCell($w, 25, $l, $m + 21, '<h1 style="font-size: 552%">FOR PALESTINE</h1>', 0, 1, false, true, 'C', false);
    $kaft->setColorArray('text', $WHITE);
    $kaft->setFont('anton', '', 32);
    $kaft->writeHTMLCell($w, 20, $l, 104, "<p>CATALOGUS</p>", 0, 1, false, true, 'C', false);
    $kaft->setColorArray('text', $BLACK);
    if ($page != 0) $kaft->movePage($kaft->PageNo(), $page);
}

function coverBack($kaft, $SPONSORS, $ROW, $w, $h, $m, $l, $SPACER)
{
    $i = count($SPONSORS);
    foreach ($SPONSORS as $s) if (isset($s['logo']) && $s['logo'] ==  false) $i--;
    $sponsorWIDTH = $w / $ROW - $SPACER;
    $sponsorHEIGHT = 90 / 120 * $sponsorWIDTH;
    $sponsorPITCH = $w / $ROW;
    $sponsorLEFT = $l;
    $x = $sponsorLEFT + $SPACER / 2;
    $y = $h - (($i - $i % $ROW) / $ROW - 1) * ($sponsorHEIGHT + $SPACER) - $m;
    $kaft->setFont('helvetica', '', 8);
    foreach ($SPONSORS as $code => $sponsor) {
        if (isset($sponsor['logo']) && $sponsor['logo'] ==  false) continue;
        $logoPNG = "./sponsors/{$code}.png";
        $logoSVG = "./sponsors/{$code}.svg";
        if (file_exists($logoPNG)) {
            $kaft->Image($logoPNG, $x, $y, $sponsorWIDTH, 0);
        } else if (file_exists($logoSVG)) {
            $kaft->ImageSVG($logoSVG, $x, $y, $sponsorWIDTH, 0);
        } else {
            $kaft->writeHTMLCell($sponsorWIDTH, $sponsorHEIGHT, $x, $y, "<p>{$sponsor['name']}</p>", 1, 1, false, true, 'C', false);
        }
        $i--;
        $x += $sponsorPITCH;
        if ($x > $w) {
            if ($i > $ROW) {
                $x = $sponsorLEFT + $SPACER / 2;
            } else {
                $x = $sponsorLEFT + $SPACER / 2 + ($ROW - $i) * $sponsorPITCH / 2;
            }
            $y += $sponsorHEIGHT + $SPACER;
        }
    }
}

// CREATE PDF of RIGHT TYPE
if ($TYPE != '') {
    $catalog = new PdfCatalog($SIZE_W, $SIZE_H);
    $catalog->SetMargins($MARGIN, $MARGIN, null, true);
    $catalog->SetAutoPageBreak(false);
    $catalog->SetBooklet(true, $MARGIN, $MARGIN + $GUTTER);
    if ($COMPRESS) $catalog->setJPEGQuality(75);
    else $catalog->setJPEGQuality(90);

    // PREFACE
    $afronden = ((count($art) - count($art) % 10) / 10);
    $cijfers = ['nul', 'tien', 'twintig', 'dertig', 'veertig', 'vijftig', 'zestig', 'zeventig', 'tachtig', 'negentig', 'honderd', 'honderdentien'];
    $AANTAL = $cijfers[$afronden];
    $catalog->AddSectionPage('Voorwoord', $RED, $WHITE, $WIDTH, $leftODD, 400);
    $catalog->AddPage();
    $catalog->setFont('anton', '', 7);
    $catalog->setColorArray('text', $RED);
    $catalog->writeHTMLCell($WIDTH, 25, $leftEVEN, $MARGIN, '<h1 style="font-size: 500%">ART &amp; DESIGN</h1>', 0, 1, false, true, 'L', false);
    $catalog->setColorArray('text', $GREEN);
    $catalog->writeHTMLCell($WIDTH, 25, $leftEVEN, $MARGIN + 12, '<h1 style="font-size: 460%">FOR PALESTINE</h1>', 0, 1, false, true, 'L', false);
    $catalog->setColorArray('text', $BLACK);
    $catalog->setFont('helvetica', '', 9);
    $catalog->writeHTMLCell($WIDTH, 25, $leftEVEN, $MARGIN + 22, "
        <p></p>
        <p>Meer dan {$AANTAL} kunstenaars en designers schonken hun werk voor deze veiling: schilderijen, beelden en designobjecten die samen een uniek en divers geheel vormen. Elk stuk is niet alleen een uitdrukking van creativiteit, maar ook van solidariteit.</p>
        <p>De opbrengst gaat integraal naar {$DOELENLIJST}. Drie organisaties die dagelijks het verschil maken, en die we met dit initiatief extra willen ondersteunen. Uw aanwezigheid en biedingen zorgen ervoor dat kunst hier méér wordt dan bewondering alleen: ze wordt een daad van verbondenheid.</p>
        <p>Onze dank gaat ook uit naar onze sponsors: {$SPONSORLIST}. Dankzij hun steun kunnen we dit evenement niet alleen mogelijk maken, maar ook aangenaam, feestelijk en net iets minder dorstig.</p>
        <p>Blader gerust, kies met uw hart, en laat u meeslepen door de energie van de veiling. Want uiteindelijk wint niet enkel de hoogste bieder, maar vooral de mensen en doelen die we samen een stap vooruit helpen.</p>
        <p></p>
        <p>Waar kunst en solidariteit elkaar raken, ontstaat hoop.</p>
        <p></p>
        <p>Met warmte en dankbaarheid,</p>
        <p>Fred, Evelyn, Peter, Pieter & Jasmien</p>
        ");

    // DOELEN
    $catalog->AddPage();
    $catalog->setColorArray('text', $BLACK);
    $catalog->setFont('helvetica', '', 9);
    $table = "<table>";
    foreach ($DOELEN as $code => $doel) {
        $table .= "<tr><td style=\"width: 1.5cm\"></td><td style=\"width: 3cm\"><img height=\"2cm\" src=\"./sponsors/{$code}.png\"></td><td style=\"width: 14cm\"><p><span style=\"font-family: anton; font-size: 125%;\">{$doel['name']}</span><br>{$doel['desc']}</p><p>&nbsp;</p></td></tr>";
    }
    $table .= "</table>";
    $catalog->writeHTML($table);
    // $catalog->AddSectionPage('', $GREEN, $WHITE, $WIDTH, $leftODD);
    // $catalog->AddSectionPage("Foto's en uitleg bij alle werken", $GREEN, $WHITE, $WIDTH, $leftODD, 400);


    // ART
    $s = 1;
    $section = 0;
    $prev = 'brol';
    foreach ($art as $code => $artwork) {

        // DATA
        $work = $artwork['WerkHoofdlettersKorter'];
        $bio = $artwork['BiografieHelveticaBlack'];
        $over = $artwork['OverWerkelveticaBlack'];
        $lot = strtoupper($artwork['Lot']);

        // EXTRA
        $COL = $COL_ORIGINAL;
        if (in_array($code, ['vanderborght', 'goethals1', 'ojomo'])) {
            $COL = $COL + 1 * $SPACER;
        } else if (in_array($code, ['hermans1', 'spierenburg', 'vanhaverbeke2', 'jordyarthur1', 'vermoesen', 'opdebeeck', 'albers', 'laporta', 'tweelinckx', 'stappaerts1', 'lagrange', 'raemdonck', 'vertommen', 'verhaegen', 'vrints', 'teddies', 'steel', 'lezy', 'oim1', 'timm', 'pola', 'schillemans', 'dhondt', 'vercaigne', 'nhannes', 'domen', 'subtitles', 'sharpart'])) {
            $COL = $COL - 1.5 * $SPACER;
        }

        // CATEGORIE
        $t = substr($lot, 0, 1);
        if ($t != $prev) {
            $section++;
            $cat = ['1' => 'Veiling op papier', '2' => 'Live bieden'];
            $categorie = isset($cat[$t]) ? $cat[$t] : 'ONBEKEND';
            while ($catalog->getNumPages() % 2 == 0) {
                $catalog->AddSectionPage('', $section % 2 == 1 ? $GREEN : $RED, $WHITE, $WIDTH, $leftODD);
                $s++;
            }
            $catalog->AddSectionPage('', $section % 2 == 1 ? $GREEN : $RED, $WHITE, $WIDTH, $leftODD);
            $catalog->AddSectionPage($categorie, $section % 2 == 1 ? $GREEN : $RED, $WHITE, $WIDTH, $leftODD, 400);
            $prev = $t;
        }

        // FORMAT
        $info = "<p><span style=\"font-family: anton; font-size: 125%;\">Lot {$lot}</span><br>";
        if ($artwork['Formaat'] != "") $info .= "{$artwork['Formaat']}<br>";
        $info .= "Bieden start bij " . (is_numeric($artwork['Prijs']) ? euro($artwork['Prijs']) : $artwork['Prijs']);
        $info .= "</p>";

        // NEW SLIDE
        $s++;
        $catalog->AddPage();
        $LEFT = $s % 2 == 1 ? $MARGIN + $GUTTER : $MARGIN;
        $align = $s % 2 == 1 ? 'left' : 'right';
        $_align = $s % 2 == 1 ? 'right' : 'left';

        // START TITLES
        $catalog->setLineStyle(['width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $BLACK]);
        $catalog->setFont('anton', '', 7);
        $catalog->setColorArray('text', $RED);
        $html =  "<h1 style=\"font-size: 250%; text-align: {$_align};\">{$work}</h1>";
        if ($s % 2 == 1) {
            $catalog->writeHTMLCell($WIDTH - $COL, 8, $LEFT + $COL, $MARGIN, $html, 0);
        } else {
            $catalog->writeHTMLCell($WIDTH - $COL, 8, $LEFT, $MARGIN, $html, 0);
        }
        $catalog->setFont('anton', '', 5);
        $catalog->setColorArray('text', $GREEN);
        $html = "<h1 style=\"font-size: 250%; text-align: {$_align};\">{$artwork['KunstDesignerAntonGreenSmaller']}</h1>";
        if ($s % 2 == 1) {
            $catalog->writeHTMLCell($WIDTH - $COL, 8, $LEFT + $COL, $MARGIN + 8, $html, 0);
        } else {
            $catalog->writeHTMLCell($WIDTH - $COL, 8, $LEFT, $MARGIN + 8, $html, 0);
        }
        $catalog->setFont('helvetica', '', 8.5);
        $catalog->setColorArray('text', $BLACK);
        $html = $over == '' ? '' : "<p><span style=\"font-family: anton; font-size: 125%;\">Over het werk</span><br>{$over}</p>";
        $html .= $bio == '' ? '' : "<p><span style=\"font-family: anton; font-size: 125%;\">Biografie {$artwork['KunstDesignerAntonBlackSmaller']}</span><br>{$bio}</p>";
        if ($s % 2 == 1) {
            $catalog->writeHTMLCell($COL, 0 /*$HEIGHT - 12*/, $LEFT, $MARGIN, $html, 0, 1);
        } else {
            $catalog->writeHTMLCell($COL, 0 /*$HEIGHT - 12*/, $LEFT + $WIDTH - $COL, $MARGIN, $html, 0, 1);
        }
        if ($artwork['Instagram'] != '') {
            $instaH = $catalog->getY() + 2;
            $instaL = $LEFT + 1 + ($s % 2 == 1 ? 0 : $WIDTH - $COL);
            $catalog->ImageSVG('/images/instagram.svg', $instaL, $instaH - 0.25, 4.25);
            $catalog->writeHTMLCell($COL - 10, 0, $instaL + 5, $instaH, "<p>{$artwork['Instagram']}</p>");
        }

        if ($s % 2 == 1) {
            $catalog->writeHTMLCell($COL, 12, $LEFT, $MARGIN + $HEIGHT - 12, $info, 0);
        } else {
            $catalog->writeHTMLCell($COL, 12, $LEFT + $WIDTH - $COL, $MARGIN + $HEIGHT - 12, $info, 0);
        }
        // POSITIONING and SIZE of IMAGES
        if ($s % 2 == 1) {
            $x1 = round($LEFT + $COL + $SPACER);
            $x2 = round($LEFT + $WIDTH);
        } else {
            $x1 = round($LEFT);
            $x2 = round($LEFT + $WIDTH - $COL - $SPACER);
        }
        $y1 = round($MARGIN + 16 + $SPACER);
        $y2 = round($MARGIN + $HEIGHT);

        // CREATE IMAGES
        if (is_array($artwork['im'])) {
            $count = count($artwork['im']);
            if ($count > 0) {
                $height = (($y2 - $y1) - ($count - 1) * $SPACER) / $count;
                foreach ($artwork['im'] as $pic => $image) {
                    if ($COMPRESS) $image = thumbnail($image);
                    list($orig_W, $orig_H) = getimagesize($image);
                    $imH = round($height);
                    $imW = round($orig_W * $imH / $orig_H);

                    if ($imW > $x2 - $x1) {
                        $imW = $x2 - $x1;
                        $imH = round($orig_H * $imW / $orig_W);
                    }

                    $catalog->Image(
                        $image,
                        ($s % 2 == 1) ? $x2 - $imW : $x1,
                        $y2 - $imH,
                        $imW,
                        $imH,
                    );
                    $y2 -= round($height + $SPACER);
                }
            } else {
                $catalog->setLineStyle(['width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => $BLACK]);
                $catalog->Rect($x1, $y1, $x2 - $x1, $y2 - $y1, 'L', [], $WHITE);
                $catalog->Line($x1, $y1, $x2, $y2);
                $catalog->Line($x1, $y2, $x2, $y1);
            }
        }
    }

    // SUMMARY
    while ($catalog->getNumPages() % 2 == 0) $catalog->AddSectionPage('', $RED, $WHITE, $WIDTH, $leftODD);
    $catalog->AddSectionPage('', $RED, $WHITE, $WIDTH, $leftODD);
    $catalog->AddSectionPage('Overzicht van alle loten', $RED, $WHITE, $WIDTH, $leftODD, 400);
    $table = [];
    $i = 1;
    foreach ($art as $code => $artwork) {
        $artist = hearts($artwork['KunstDesignerShort'], 7);
        $work = $artwork['Werk'];
        $price = $artwork['PrijsEuro'];
        $number = $artwork['Lot'];
        $row = '<tr>';
        $row .= "<td width=\"2cm\" style=\"text-align: left\">{$number}</td>";
        $row .= "<td width=\"6cm\" style=\"text-align: left\">{$artist}</td>";
        $row .= "<td width=\"7cm\" style=\"text-align: left\">{$work}</td>";
        $row .= "<td width=\"2.5cm\" style=\"text-align: right\">{$price}</td>";
        $row .= '</tr>';
        $table[] = $row;
    }
    $header = '<tr style="font-weight: bold">';
    $header .= "<td width=\"2cm\" style=\"text-align: left\">Lot</td>";
    $header .= "<td width=\"6cm\" style=\"text-align: left\">Kunstenaar / ontwerper</td>";
    $header .= "<td width=\"7cm\" style=\"text-align: left\">Titel of omschrijving van het item</td>";
    $header .= "<td width=\"2.5cm\" style=\"text-align: right\">Prijs</td>";
    $header .= '</tr>';
    $catalog->setColorArray('text', $BLACK);
    $step = 30;
    $evenOdd = 0;
    for ($i = 0; $i < count($table); $i += $step) {
        $evenOdd++;
        $catalog->AddPage();
        $catalog->setFont('anton', '', 9);
        $html = '<table>' . $header . '</table>';
        if ($evenOdd % 2 == 0) $html = str_replace('<td width="2cm"', '<td width="1cm"></td><td width="2cm"', $html);
        $catalog->writeHTML($html);
        $catalog->Ln(-2);
        $catalog->setFont('helvetica', '', 9);
        $html = '<table>' . implode('', array_slice($table, $i, $step)) . '</table>';
        if ($evenOdd % 2 == 0) $html = str_replace('<tr>', '<tr><td width="1cm"></td>', $html);
        $catalog->writeHTML($html);
    }

    // EXTRA PAGES
    if ($TYPE == 'alles') {
        $catalog->AddSectionPage('', $GREEN, $WHITE, $WIDTH, $leftODD);
    } else {
        while ($catalog->getNumPages() % 4 != 0) {
            $catalog->AddSectionPage('', $GREEN, $WHITE, $WIDTH, $leftODD);
        }
    }
}

if ($TYPE == 'binnenwerk') {
    $catalog->output("catalog-{$timestamp}.pdf");
} else if ($TYPE == 'alles') {
    coverFront($catalog, $WIDTH, $SIZE_H, $MARGIN, $MARGIN + $GUTTER, 1);
    $catalog->AddPage();
    $catalog->setFillColorArray([167, 219, 208]);
    $catalog->Rect(0, 0, $SIZE_W, $SIZE_H, 'F');
    coverBack($catalog, $SPONSORS, 5, $WIDTH, $HEIGHT, $MARGIN, $MARGIN, $SPACER);
    $catalog->output("catalogus-lowres-{$timestamp}.pdf");
} else if ($TYPE == 'kaft') {
    $THICKNESS = 11;
    $COVER = 2 * $SIZE_W + $THICKNESS - 2 * $BLEED;
    $kaft = new PdfCatalog($COVER, $SIZE_H);
    $kaft->setMargins(0, 0);
    $kaft->SetAutoPageBreak(false);

    $kaft->setFont('anton', '', 10);

    // front
    coverFront($kaft, $WIDTH, $SIZE_H, $MARGIN, $SIZE_W + $THICKNESS + $leftODD);

    // back
    coverBack($kaft, $SPONSORS, 5, $WIDTH, $HEIGHT, $MARGIN, $leftEVEN, $SPACER);


    // inside
    $kaft->AddPage();
    $kaft->Rect(0, 0, $SIZE_W - 4/*+ $THICKNESS / 2*/, $SIZE_W, 'F', [], $RED);
    $kaft->Rect($SIZE_W + $THICKNESS + 4, 0, $SIZE_W - 4, $SIZE_W, 'F', [], $GREEN);

    $kaft->output("cover-{$timestamp}.pdf");
}
