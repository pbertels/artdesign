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

$W = 210;
$H = 297;
$MARGIN = 10;
$WIDTH = $W - 2 * $MARGIN;
$HEIGHT = $H - 2 * $MARGIN;
$SPACER = 10;
$IMAGE = 25;
$C = 3;
$R = 5;
$SIZE_X = ($WIDTH - ($C - 1) * $SPACER) / $C;
$SIZE_Y = ($HEIGHT - ($R - 1) * $SPACER) / $R;

$timestamp = date('Ymd-Hi');

$silent = new PdfCatalog($W, $H);
$silent->SetMargins($MARGIN, $MARGIN, null, true);
$silent->SetAutoPageBreak(false);

$x = $MARGIN;
$c = 0;
$y = 2 * $H;
$r = 2 * $R;

// LOOP
foreach ($art as $code => $artwork) {

    // DATA
    $work = strtoupper($artwork['Werk']);
    $work = str_replace(['é', 'â'], ['E', 'A'], $work);
    $artist = $artwork['KunstDesigner'];
    $lot = $artwork['Lot'];

    // NEW PAGE
    if ($r >= $R) {
        $x = $MARGIN;
        $c = 0;
        $y = $MARGIN;
        $r = 0;
        $silent->AddPage();
    }

    // RECTANGLE
    $silent->writeHTMLCell($SIZE_X, $SIZE_Y, $x, $y, "<p></p>", 1, 0, 0, 0, 'C');

    // IMAGE
    if (is_array($artwork['im']) && count($artwork['im']) > 0) {
        $image = $artwork['im'][array_key_first($artwork['im'])];
        list($orig_W, $orig_H) = getimagesize($image);
        $imW = (int) $SIZE_X - $SPACER;
        $imH = (int) ($orig_H * $imW / $orig_W);

        if ($imH > $SIZE_Y - $SPACER) {
            $imH = (int) $SIZE_Y - $SPACER;
            $imW = (int) ($orig_W * $imH / $orig_H);
        }

        $dx = ($SIZE_X - $imW) / 2;
        $dy = ($SIZE_Y - $imH) / 2;
        $silent->Image($image, $x + $dx, $y + $dy, $imW, $imH);
    }

    // ARTWORK
    $silent->setFont('helvetica', '', 9);
    $silent->setTextColorArray($BLACK);
    $silent->writeHTMLCell($SIZE_X, $SIZE_Y, $x, $y, "<p></p>", 1, 0, 0, 0, 'L');
    $silent->setXY($x, $y);
    $silent->write(0, "{$lot} / {$artist}");

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

// OUTPUT
$silent->output("overview-{$timestamp}.pdf");
