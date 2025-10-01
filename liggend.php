<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/sponsors.php';
require __DIR__ . '/parse.php';

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Alignment;

// Read template
$pptReader = IOFactory::createReader('PowerPoint2007');
$presentation = $pptReader->load(__DIR__ . '/liggend.pptx');

// Text
function text($slide, $x, $y, $w, $h, $text, $font = 'Arial', $bold = false, $size = 24, $align = 'L', $hexColour = 'FF000000')
{
    $alignment = Alignment::HORIZONTAL_CENTER;
    switch ($align) {
        case 'L':
            $alignment = Alignment::HORIZONTAL_LEFT;
            break;
        case 'C':
            $alignment = Alignment::HORIZONTAL_CENTER;
            break;
        case 'R':
            $alignment = Alignment::HORIZONTAL_RIGHT;
            break;
    }
    $shape = $slide->createRichTextShape()
        ->setHeight(round($h))
        ->setWidth(round($w))
        ->setOffsetX(round($x))
        ->setOffsetY(round($y));
    $shape->getActiveParagraph()->getAlignment()
        ->setHorizontal($alignment);
    $lines = explode("\n", str_replace('<br>', "\n", $text));
    foreach ($lines as $line) {
        $paragraph = $line;
        if (substr($line, 0, 3) == '<b>') {
            $paragraph = substr($line, 3, -4);
            $b = true;
        } else {
            $b = false;
        }
        $f = $b ? 'Anton' : $font;
        $s = $b ? $size + 1 : $size;
        $textRun = $shape->createTextRun("{$paragraph}\n");
        $textRun->getFont()
            ->setName($f)
            ->setBold($bold || $b)
            ->setSize($s)
            ->setColor(new Color($hexColour));
    }
}

// Variables
$W = 808;
$mm = $W / 214;
$H = round(152 * $mm);
$COL75 = 90 * $mm;
$MARGIN_TOP = round(10 * $mm);
$MARGIN_BOTTOM = $MARGIN_TOP;
$GUTTER = round(10 * $mm);
$MARGIN_ODD = $MARGIN_TOP + $GUTTER;
$MARGIN_EVEN = $MARGIN_TOP;
$SPACER = $MARGIN_BOTTOM / 2;
$WIDTH = $W - $MARGIN_ODD - $MARGIN_EVEN;
$HEIGHT = $H - $MARGIN_TOP - $MARGIN_BOTTOM;
$RED = 'FFEB5A3C';
$GREEN = 'FF507F23';
$BLACK = 'FF000000';

// TEST
// $slide = $presentation->createSlide();

// $art = array_slice($art, 0, 3);

// Loop Artworks
$s = 0;
foreach ($art as $code => $artwork) {

    $work = $artwork['WerkHoofdlettersKorter'];
    $work = str_replace('<BR>', '', $work);
    $work = html_entity_decode($work, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $artist = html_entity_decode($artwork['KunstDesigner'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $artist = str_replace(' en ', "\nen ", $artist);
    $bio = html_entity_decode($artwork['Biografie'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $over = html_entity_decode($artwork['OverWerk'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $lot = strtoupper($artwork['Lot']);

    // format text
    $OverEnBio = "";
    if ($over != "") $OverEnBio .= "<b>Over het werk</b>\n{$over}\n\n";
    if ($bio != "") $OverEnBio .= "<b>Biografie</b>\n{$bio}";
    $info = "<b>Lot {$lot}</b>\n";
    if ($artwork['Formaat'] != "") $info .= "{$artwork['Formaat']}\n";
    $info .= "Bieden start bij " . html_entity_decode(is_numeric($artwork['Prijs']) ? euro($artwork['Prijs']) : $artwork['Prijs'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // new slide
    $s++;
    $slide = $presentation->createSlide();
    $LEFT = $s % 2 == 1 ? $MARGIN_ODD : $MARGIN_EVEN;
    $align = $s % 2 == 1 ? 'L' : 'R';
    $_align = $s % 2 == 1 ? 'R' : 'L';

    // odd or even
    if ($s % 2 == 1) {
        text($slide, $LEFT + $COL75, $MARGIN_TOP, $WIDTH - $COL75, 8 * $mm, $work, 'Anton', true, 16, $_align, $RED);
        text($slide, $LEFT + $COL75, $MARGIN_TOP + 8 * $mm, $WIDTH - $COL75, 8 * $mm, $artist, 'Anton', true, 12, $_align, $GREEN);
        text($slide, $LEFT, $MARGIN_TOP, $COL75, $HEIGHT - 15 * $mm, $OverEnBio, 'Arial', false, 9, 'L', $BLACK);
        text($slide, $LEFT, $MARGIN_TOP + $HEIGHT - 15 * $mm, $COL75, 15 * $mm, $info, 'Arial', false, 9, 'L', $BLACK);
        $x1 = round($LEFT + $COL75);
        $x2 = round($LEFT + $WIDTH);
        $y1 = round($MARGIN_TOP + 16 * $mm);
        $y2 = round($MARGIN_TOP + $HEIGHT);
    } else {
        text($slide, $LEFT, $MARGIN_TOP, $WIDTH - $COL75, 8 * $mm, $work, 'Anton', true, 16, $_align, $RED);
        text($slide, $LEFT, $MARGIN_TOP + 8 * $mm, $WIDTH - $COL75, 8 * $mm, $artist, 'Anton', true, 12, $_align, $GREEN);
        text($slide, $LEFT + $WIDTH - $COL75, $MARGIN_TOP, $COL75, $HEIGHT - 15 * $mm, $OverEnBio, 'Arial', false, 9, 'L', $BLACK);
        text($slide, $LEFT + $WIDTH - $COL75, $MARGIN_TOP + $HEIGHT - 15 * $mm, $COL75, 15 * $mm, $info, 'Arial', false, 9, 'L', $BLACK);
        $x1 = round($LEFT);
        $x2 = round($LEFT + $WIDTH - $COL75);
        $y1 = round($MARGIN_TOP + 16 * $mm);
        $y2 = round($MARGIN_TOP + $HEIGHT);
    }

    // picture positioning
    if ($s % 2 == 1) {
        $x1 += $SPACER;
    } else {
        $x2 -= $SPACER;
    }
    $y1 += $SPACER;

    if (is_array($artwork['im'])) {
        $count = count($artwork['im']);
        if ($count > 0) {
            $height = (($y2 - $y1) - ($count - 1) * $SPACER) / $count;
            foreach ($artwork['im'] as $pic => $image) {
                list($orig_W, $orig_H) = getimagesize($image);
                $imH = round($height);
                $imW = round($orig_W * $imH / $orig_H);

                if ($imW > $x2 - $x1) {
                    $imW = $x2 - $x1;
                    $imH = round($orig_H * $imW / $orig_W);
                }

                $shape = $slide->createDrawingShape();
                $shape->setName('image')
                    ->setDescription('description')
                    ->setPath($image)
                    ->setWidth($imW)
                    ->setHeight($imH)
                    ->setOffsetX(($s % 2 == 1) ? $x2 - $imW : $x1)
                    ->setOffsetY($y2 - $imH);
                $y2 -= round($height + $SPACER);
            }
        }
    }
}

// Write
$timestamp = date('Ymd-Hi');
$pptx = "/output/alternatief-{$timestamp}.pptx";
$oWriterPPTX = IOFactory::createWriter($presentation, 'PowerPoint2007');
$oWriterPPTX->save(__DIR__ . $pptx);

// Output
require __DIR__ . '/header.php';
echo '<h1>Art &amp; Design for Palestine</h1>';
echo "<p>Download: <a href=\".{$pptx}\">download catalogus alternatief formaat</a></p>";
