<?php

ini_set("memory_limit", -1);

use ArtDesign\PdfCatalog;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/sponsors.php';
require __DIR__ . '/parse.php';

// DEFINITIONS
$RED = [235, 90, 60];
$GREEN = [80, 127, 35];
$BLUE = [0, 0, 255];
$BLACK = [0, 0, 0];
$WHITE = [255, 255, 255];

$TYPE = isset($_GET['type']) ? $_GET['type'] : 'veilingmeester';
$REAL = isset($_GET['real']) ? true : false;

$W = 210;
$H = 297;
$MARGIN = 10;
$SPACER = 2 * $MARGIN;
$frontback = ['front', 'back'];

switch ($TYPE) {
    case 'veilingmeester':
        $C = 1;
        $R = 2;
        $frontback = ['veilingmeester'];
        $SPACER = 4;
        $IMAGE_SCALE = 0.5;
        foreach ($art as $code => $artwork) {
            $lot = $artwork['Lot'];
            if ($lot < 200) unset($art[$code]);
        }
        break;
    case 'square':
        $C = 2;
        $R = 3;
        break;
    default:
        $C = 3;
        $R = 5;
        break;
}

$IMAGE = 25;
$WIDTH = $W - 2 * $MARGIN;
$HEIGHT = $H - 2 * $MARGIN;
$SIZE_X = ($WIDTH - ($C - 1) * $SPACER) / $C;
$SIZE_Y = ($HEIGHT - ($R - 1) * $SPACER) / $R;

$timestamp = date('Ymd-Hi');

$kaartjes = new PdfCatalog($W, $H);
$kaartjes->SetMargins($MARGIN, $MARGIN, null, true);
$kaartjes->SetAutoPageBreak(false);

// LOOP
for ($i = 0; $i < count($art); $i += $R * $C) {
    $slice = array_slice($art, $i, $R * $C);

    foreach ($frontback as $side) {
        // NEW PAGE
        $x = $MARGIN;
        $c = 0;
        $y = $MARGIN;
        $r = 0;
        $kaartjes->AddPage();

        // ORDERING
        if ($side == 'back') {
            $order = [];
            for ($j = 0; $j < $R * $C - 1; $j += 2) {
                $next = array_slice($slice, $j + 1, 1);
                if (count($next) == 0) {
                    $dummy = array_pop($art);
                    $next = ['test' => $dummy];
                }
                $order = array_merge($order, $next);
                $order = array_merge($order, array_slice($slice, $j, 1));
            }
        } else {
            $order = $slice;
        }

        // CREATE ALL ITEMS
        foreach ($order as $code => $artwork) {

            // DATA
            $work = strtoupper($artwork['WerkHoofdlettersKorter']);
            $artist = $artwork['KunstDesignerAntonGreenSmaller'];
            $lot = $artwork['Lot'];

            // RECTANGLE
            if (!$REAL) $kaartjes->writeHTMLCell($SIZE_X, $SIZE_Y, $x, $y, "<p></p>", 1, 0, 0, 0, 'C');

            // FRONT
            if ($side == 'front') {
                $work = str_replace(['T SAM', ', FR', 'S - G', ' (', 'R- NO'], ['T<br>SAM', '<br>FR', 'S<br>G', '<br>(', 'R<br>NO'], $work);
                $kaartjes->setFont('anton', '', 36);
                $kaartjes->setTextColorArray($BLACK);
                $kaartjes->writeHTMLCell($SIZE_X, $SPACER / 2, $x, $y + 10, "{$lot}", 0, 0, 0, true, 'C');
                $kaartjes->setFont('anton', '', 24);
                $kaartjes->setTextColorArray($RED);
                $kaartjes->writeHTMLCell($SIZE_X, $SPACER / 2, $x, $y + 35, "{$work}", 0, 1, 0, true, 'C');
                $kaartjes->setFont('anton', '', 18);
                $kaartjes->setTextColorArray($GREEN);
                $kaartjes->writeHTMLCell($SIZE_X, $SPACER / 2, $x, $kaartjes->GetY() + 2, "{$artist}", 0, 0, 0, true, 'C');
            }
            // BACK
            else if ($side == 'back') {
                if (is_array($artwork['im']) && count($artwork['im']) > 0) {
                    $image = $artwork['im'][array_key_first($artwork['im'])];
                    $image = thumbnail($image);
                    list($orig_W, $orig_H) = getimagesize($image);
                    $imW = (int) $SIZE_X - $SPACER;
                    $imH = (int) ($orig_H * $imW / $orig_W);

                    if ($imH > $SIZE_Y - $SPACER) {
                        $imH = (int) $SIZE_Y - $SPACER;
                        $imW = (int) ($orig_W * $imH / $orig_H);
                    }

                    $dx = ($SIZE_X - $imW) / 2;
                    $dy = ($SIZE_Y - $imH) / 2;
                    $kaartjes->Image($image, $x + $dx, $y + $dy + 8, $imW, $imH);
                }
                $kaartjes->setFont('helvetica', '', 8);
                $kaartjes->setTextColorArray($BLACK);
                $kaartjes->setXY($x, $y);
                $kaartjes->Cell($SIZE_X, 0, "Lot {$lot}", 0, 0, 'C');
                $kaartjes->setFont('anton', '', 9);
                $kaartjes->setTextColorArray($RED);
                $kaartjes->writeHTMLCell($SIZE_X, $SPACER / 2, $x, $y + 4, "{$work}", 0, 0, 0, true, 'C');
                $kaartjes->setTextColorArray($GREEN);
                $kaartjes->writeHTMLCell($SIZE_X, $SPACER / 2, $x, $y + 8, "{$artist}", 0, 0, 0, true, 'C');
            }
            // BACK
            else if ($side == 'veilingmeester') {
                if (is_array($artwork['im']) && count($artwork['im']) > 0) {
                    $image = $artwork['im'][array_key_first($artwork['im'])];
                    $image = thumbnail($image);
                    list($orig_W, $orig_H) = getimagesize($image);
                    $imW = (int) (($SIZE_Y - $SPACER) * $IMAGE_SCALE);
                    $imH = (int) ($orig_H * $imW / $orig_W);

                    if ($imH > ($SIZE_Y - $SPACER) * 0.8) {
                        $imH = (int) $SIZE_Y - $SPACER;
                        $imW = (int) ($orig_W * $imH / $orig_H);
                    }

                    $dx = ($SIZE_X - $imW) / 2;
                    $dy = ($SIZE_Y - $imH) / 2;
                    $kaartjes->Image($image, $SIZE_X - $imW + $MARGIN, $y + $dy, $imW, $imH);
                }
                $kaartjes->setFont('helvetica', 'B', 13);
                $kaartjes->setTextColorArray($BLACK);
                $kaartjes->setXY($x, $y);
                $kaartjes->writeHTMLCell($SIZE_X, $SPACER / 2, $x, $y, "Lot {$lot} - start aan {$artwork['PrijsEuro']}<br>{$work} - {$artist}", 0, 1, 0, true, 'L');
                $kaartjes->setFont('helvetica', '', 10);
                if ($artwork['Formaat'] != '') $kaartjes->writeHTMLCell($SIZE_X - $SIZE_Y * $IMAGE_SCALE, $SPACER / 2, $x, $kaartjes->GetY(), "Formaat: {$artwork['Formaat']}", 0, 1, 0, true, 'L');
                $extra = $artwork['ExtraInfo'];
                if (strlen($extra) > 0) {
                    $kaartjes->setFillColorArray([255, 255, 0]);
                    $kaartjes->writeHTMLCell($SIZE_X - $SIZE_Y * $IMAGE_SCALE, $SPACER / 2, $x, $kaartjes->GetY(), "{$extra}", 0, 1, 1, true, 'L');
                }
                if ($artwork['OverWerkelveticaBlack'] != '') {
                    $over = $artwork['OverWerkelveticaBlack'];
                    // $over = strpos($over, '<i>') === false ? $over : substr($over, 0, strpos($over, '<i>'));
                    $kaartjes->writeHTMLCell($SIZE_X - $SIZE_Y * $IMAGE_SCALE, $SPACER / 2, $x, $kaartjes->GetY() + 3, "<p><strong>Over dit werk:</strong> {$over}</p>", 0, 1, 0, true, 'L');
                }
                if ($artwork['BiografieHelveticaBlack'] != '') {
                    $bio = $artwork['BiografieHelveticaBlack'];
                    $kaartjes->writeHTMLCell($SIZE_X - $SIZE_Y * $IMAGE_SCALE, $SPACER / 2, $x, $kaartjes->GetY() + 3, "<p><strong>Biografie</strong>: {$bio}</p>", 0, 1, 0, true, 'L');
                }
                // $kaartjes->Cell($SIZE_X, 0, "Lot {$lot}", 0, 0, 'L');
                // $kaartjes->setFont('anton', '', 12);
                // $kaartjes->setTextColorArray($RED);
                // $kaartjes->writeHTMLCell($SIZE_X, $SPACER / 2, $x, $y + 6, "{$work}", 0, 0, 0, true, 'L');
                // $kaartjes->setTextColorArray($GREEN);
                // $kaartjes->writeHTMLCell($SIZE_X, $SPACER / 2, $x, $y + 12, "{$artist}", 0, 0, 0, true, 'L');
            }

            // JUMP
            $x += $SIZE_X + $SPACER;
            $c++;
            if ($c >= $C) {
                $x = $MARGIN;
                $y += $SIZE_Y + $SPACER;
                $r++;
                $c = 0;
            }
        }
    }
}

// OUTPUT
$kaartjes->output("overview-{$timestamp}.pdf");
