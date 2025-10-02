<?php

ini_set("memory_limit", -1);

use ArtDesign\PdfCatalog;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/sponsors.php';
require __DIR__ . '/parse.php';

// TYPE
$TYPE = isset($_GET['type']) && in_array($_GET['type'], ['binnenwerk', 'kaft']) ? $_GET['type'] : 'binnenwerk';
$COMPRESS = isset($_GET['compress']);

// DEFINITIONS
$RED = [235, 90, 60];
$GREEN = [80, 127, 35];
$BLUE = [0, 0, 255];
$BLACK = [0, 0, 0];
$WHITE = [255, 255, 255];
$BLEED = 2;
$SIZE_W = 210 + 2 * $BLEED;
$SIZE_H = 148 + 2 * $BLEED;
$MARGIN = 10;
$GUTTER = 10;
$WIDTH = $SIZE_W - 2 * $MARGIN - $GUTTER;
$HEIGHT = $SIZE_H - 2 * $MARGIN;
$leftODD = $MARGIN + $GUTTER;
$leftEVEN = $MARGIN;
$SPACER = 10;
$COL = 90;
$timestamp = date('Ymd-Hi');

// CREATE PDF of RIGHT TYPE
if ($TYPE != '') {
    $catalog = new PdfCatalog($SIZE_W, $SIZE_H);
    $catalog->SetMargins($MARGIN, $MARGIN, null, true);
    $catalog->SetAutoPageBreak(false);
    $catalog->SetBooklet(true, $MARGIN, $MARGIN + $GUTTER);

    // PREFACE
    $afronden = ((count($art) - count($art) % 10) / 10);
    $cijfers = ['nul', 'tien', 'twintig', 'dertig', 'veertig', 'vijftig', 'zestig', 'zeventig', 'tachtig', 'negentig', 'honderd', 'honderdentien'];
    $AANTAL = $cijfers[$afronden];
    $catalog->AddSectionPage('Voorwoord', $RED, $WHITE, $WIDTH, $leftODD, 400);
    $catalog->AddSectionPage('', $RED, $WHITE, $WIDTH, $leftODD);
    $catalog->AddPage();
    $catalog->setFont('anton', '', 7);
    $catalog->setColorArray('text', $RED);
    $catalog->writeHTMLCell($WIDTH, 25, $leftODD, $MARGIN, '<h1 style="font-size: 500%">ART &amp; DESIGN</h1>', 0, 1, false, true, 'L', false);
    $catalog->setColorArray('text', $GREEN);
    $catalog->writeHTMLCell($WIDTH, 25, $leftODD, $MARGIN + 12, '<h1 style="font-size: 460%">FOR PALESTINE</h1>', 0, 1, false, true, 'L', false);
    $catalog->setColorArray('text', $BLACK);
    $catalog->setFont('helvetica', '', 9);
    $catalog->writeHTMLCell($WIDTH, 25, $leftODD, $MARGIN + 22, "
        <p></p>
        <p>Meer dan {$AANTAL} kunstenaars en designers schonken hun werk voor deze veiling: schilderijen, beelden en designobjecten die samen een uniek en divers geheel vormen. Elk stuk is niet alleen een uitdrukking van creativiteit, maar ook van solidariteit.</p>
        <p>De opbrengst gaat integraal naar het Rode Kruis, Oxfam en UNRWA. Drie organisaties die dagelijks het verschil maken, en die we met dit initiatief extra willen ondersteunen. Uw aanwezigheid en biedingen zorgen ervoor dat kunst hier méér wordt dan bewondering alleen: ze wordt een daad van verbondenheid.</p>
        <p>Onze dank gaat ook uit naar onze sponsors: {$SPONSORLIST}. Dankzij hun steun kunnen we dit evenement niet alleen mogelijk maken, maar ook aangenaam, feestelijk en net iets minder dorstig.</p>
        <p>Blader gerust, kies met uw hart, en laat u meeslepen door de energie van de veiling. Want uiteindelijk wint niet enkel de hoogste bieder, maar vooral de mensen en doelen die we samen een stap vooruit helpen.</p>
        <p></p>
        <p>Waar kunst en solidariteit elkaar raken, ontstaat hoop.</p>
        <p></p>
        <p>Met warmte en dankbaarheid,</p>
        <p>Fred, Evelyn, Peter, Pieter & Jasmien</p>
        ");
    $catalog->AddSectionPage('', $GREEN, $WHITE, $WIDTH, $leftODD);
    $catalog->AddSectionPage("Foto's en uitleg bij alle werken", $GREEN, $WHITE, $WIDTH, $leftODD, 400);


    // ART
    $s = 0;
    foreach ($art as $code => $artwork) {

        // DATA
        $work = $artwork['WerkHoofdlettersKorter'];
        // $artist = $artwork['KunstDesigner'];
        // $artist = str_replace(' en ', "<br>en ", $artist);
        $bio = $artwork['Biografie'];
        $over = $artwork['OverWerk'];
        $lot = strtoupper($artwork['Lot']);

        // FORMAT
        // // format text
        // $OverEnBio = "";
        // if ($over != "") $OverEnBio .= "<b>Over het werk</b>\n{$over}\n\n";
        // if ($bio != "") $OverEnBio .= "<b>Biografie</b>\n{$bio}";
        $info = "<p><b>Lot {$lot}</b><br>";
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
        $html = "<p><span style=\"font-family: anton; font-size: 125%;\">Over het werk</span><br>{$over}</p>";
        $html .= "<p><span style=\"font-family: anton; font-size: 125%;\">Biografie {$artwork['KunstDesignerAntonBlackSmaller']}</span><br>{$bio}</p>";
        if ($s % 2 == 1) {
            $catalog->writeHTMLCell($COL, $HEIGHT - 12, $LEFT, $MARGIN, $html, 0);
        } else {
            $catalog->writeHTMLCell($COL, $HEIGHT - 12, $LEFT + $WIDTH - $COL, $MARGIN, $html, 0);
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
                    if ($COMPRESS) $image = str_replace('images/', 'images/thumbnails/', $image);
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

                // $x = $leftODD;
                // $width = ($WIDTH - ($count - 1) * $SPACER) / $count;
                // foreach ($artwork['im'] as $pic => $image) {
                //     list($orig_W, $orig_H) = getimagesize($image);
                //     if ($orig_W > $orig_H) {
                //         $catalog->Image($image, $x, $MARGIN, $width, 0);
                //     } else {
                //         $catalog->Image($image, $x, $MARGIN, 0, $width);
                //     }
                //     $x += $width + $SPACER;
                // }
            } else {
                $catalog->setLineStyle(['width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => $BLACK]);
                $catalog->Rect($x1, $y1, $x2 - $x1, $y2 - $y1, 'L', [], $WHITE);
                $catalog->Line($x1, $y1, $x2, $y2);
                $catalog->Line($x1, $y2, $x2, $y1);
            }
        }
    }

    // SUMMARY
    $catalog->AddSectionPage('', $RED, $WHITE, $WIDTH, $leftODD);
    $catalog->AddSectionPage('Overzicht van alle loten', $RED, $WHITE, $WIDTH, $leftODD, 400);
    $table = [];
    $i = 1;
    foreach ($art as $code => $artwork) {
        $artist = hearts($artwork['KunstDesigner'], 8);
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
    $step = 25;
    for ($i = 0; $i < count($table); $i += $step) {
        $catalog->AddPage();
        $catalog->setFont('anton', '', 9);
        $html = '<table>' . $header . '</table>';
        $catalog->writeHTML($html);
        $catalog->setFont('helvetica', '', 9);
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
    $COVER = 2 * $SIZE_W + $THICKNESS - 2 * $BLEED;
    $kaft = new PdfCatalog($COVER, $SIZE_W);
    $kaft->setFont('anton', '', 10);

    // front
    $kaft->AddPage();
    $kaft->setColorArray('text', $RED);
    $kaft->writeHTMLCell($WIDTH, 25, $SIZE_W + $THICKNESS + $leftODD, $MARGIN, '<h1 style="font-size: 700%">ART &amp; DESIGN</h1>', 0, 1, false, true, 'C', false);
    $kaft->setColorArray('text', $GREEN);
    $kaft->writeHTMLCell($WIDTH, 25, $SIZE_W + $THICKNESS + $leftODD, $MARGIN + 25, '<h1 style="font-size: 645%">FOR PALESTINE</h1>', 0, 1, false, true, 'C', false);
    $kaft->setColorArray('text', $BLACK);
    $kaft->setFont('helvetica', '', 18);
    $kaft->writeHTMLCell($WIDTH, 25, $SIZE_W + $THICKNESS + $leftODD, $SIZE_W / 2, "<p>{$COVER} mm x {$SIZE_W} mm</p>", 0, 1, false, true, 'C', false);

    // rug
    // $kaft->Rect($SIZE_W, 0, $THICKNESS, $SIZE_W, 'F', [], $BLACK);

    // back
    $ROW = 5;
    $i = count($SPONSORS);
    foreach ($SPONSORS as $s) if (isset($s['logo']) && $s['logo'] ==  false) $i--;
    $sponsorWIDTH = $WIDTH / $ROW - $SPACER;
    $sponsorHEIGHT = 90 / 120 * $sponsorWIDTH;
    $sponsorPITCH = $WIDTH / $ROW;
    $sponsorLEFT = $leftEVEN;
    $x = $sponsorLEFT + $SPACER / 2;
    $y = $WIDTH - (($i - $i % $ROW) / $ROW) * ($sponsorHEIGHT + $SPACER);
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
        if ($x > $WIDTH) {
            if ($i > $ROW) {
                $x = $sponsorLEFT + $SPACER / 2;
            } else {
                $x = $sponsorLEFT + $SPACER / 2 + ($ROW - $i) * $sponsorPITCH / 2;
            }
            $y += $sponsorHEIGHT + $SPACER;
        }
    }

    // inside
    $kaft->AddPage();
    $kaft->Rect(0, 0, $SIZE_W - 4/*+ $THICKNESS / 2*/, $SIZE_W, 'F', [], $RED);
    $kaft->Rect($SIZE_W + $THICKNESS + 4, 0, $SIZE_W - 4, $SIZE_W, 'F', [], $GREEN);

    $kaft->output("cover-{$timestamp}.pdf");
}
