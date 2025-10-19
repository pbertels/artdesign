<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/sponsors.php';
require __DIR__ . '/parse.php';

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;

// Read template
$pptReader = IOFactory::createReader('PowerPoint2007');
$presentation = $pptReader->load(__DIR__ . '/template.pptx');

// Variables
$W = 1280;
$H = 720;
$MARGIN_TOP = 100;
$MARGIN_BOTTOM = 0.01 * $W;
$MARGIN_LEFT = $MARGIN_BOTTOM;
$SPACER = $MARGIN_BOTTOM;
$WIDTH = $W - 2 * $MARGIN_LEFT;
$HEIGHT = $H - $MARGIN_TOP - $MARGIN_BOTTOM;
$RED = 'FFEB5A3C';
$GREEN = 'FF507F23';
$BLACK = 'FF000000';

// TEST
$slide = $presentation->createSlide();

// Loop Artworks
foreach ($art as $code => $artwork) {

    $work = strtoupper($artwork['WerkHoofdletters']);
    $work = str_replace('<BR>', '', $work);
    $work = html_entity_decode($work, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $artist = html_entity_decode($artwork['KunstDesigner'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $lot = strtoupper($artwork['Lot']);

    // papier
    if ($lot < 200) continue;

    // new slide
    $slide = $presentation->createSlide();

    // number
    $shape = $slide->createRichTextShape()
        ->setHeight($MARGIN_TOP)
        ->setWidth($W)
        ->setOffsetX(0)
        ->setOffsetY(15);
    $shape->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $textRun = $shape->createTextRun("Lot {$lot}");
    $textRun->getFont()
        ->setName('Anton')
        ->setBold(true)
        ->setSize(24)
        ->setColor(new Color($BLACK));

    // work
    $shape = $slide->createRichTextShape()
        ->setHeight($MARGIN_TOP)
        ->setWidth(0.50 * $W-10)
        ->setOffsetX(10)
        ->setOffsetY(15);
    $shape->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $textRun = $shape->createTextRun("{$work}");
    $textRun->getFont()
        ->setName('Anton')
        ->setBold(true)
        ->setSize(24)
        ->setColor(new Color($RED));

    // arist
    $shape = $slide->createRichTextShape()
        ->setHeight($MARGIN_TOP)
        ->setWidth(0.50 * $W-10)
        ->setOffsetX(0.50 * $W)
        ->setOffsetY(15);
    $shape->getActiveParagraph()->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $textRun = $shape->createTextRun("{$artist}");
    $textRun->getFont()
        ->setName('Anton')
        ->setBold(true)
        ->setSize(24)
        ->setColor(new Color($GREEN));


    // images
    if (is_array($artwork['im'])) {
        $count = count($artwork['im']);
        if ($count > 0) {
            $x = $MARGIN_LEFT;
            $width = ($WIDTH - ($count - 1) * $SPACER) / $count;
            foreach ($artwork['im'] as $pic => $image) {
                list($orig_W, $orig_H) = getimagesize($image);
                $imW = (int) $width;
                $imH = (int) ($orig_H * $imW / $orig_W);

                if ($imH > $HEIGHT) {
                    $imH = (int) $HEIGHT;
                    $imW = (int) ($orig_W * $imH / $orig_H);
                }

                $shape = $slide->createDrawingShape();
                $shape->setName('image')
                    ->setDescription('description')
                    ->setPath($image)
                    ->setWidth($imW)
                    ->setHeight($imH)
                    ->setOffsetX((int) ($x + ($width - $imW) / 2))
                    ->setOffsetY($MARGIN_TOP);

                $x += (int) $width + $SPACER;
            }
        }
    }
}


// // Create slide
// $currentSlide = $objPHPPowerPoint->getActiveSlide();
// // $currentSlide->setSlideLayout(0);

// // Create a shape (drawing)
// $shape = $currentSlide->createDrawingShape();
// $shape->setName('PHPPresentation logo')
//     ->setDescription('PHPPresentation logo')
//     ->setPath('./sponsors/norsu.png')
//     ->setHeight(36)
//     ->setOffsetX(10)
//     ->setOffsetY(10);
// $shape->getShadow()->setVisible(true)
//                    ->setDirection(45)
//                    ->setDistance(10);

// Write
$timestamp = date('Ymd-Hi');
$pptx = "/output/adfp-{$timestamp}.pptx";
$oWriterPPTX = IOFactory::createWriter($presentation, 'PowerPoint2007');
$oWriterPPTX->save(__DIR__ . $pptx);

// Output
require __DIR__ . '/header.php';
echo '<h1>Art &amp; Design for Palestine</h1>';
echo "<p>Download: <a href=\".{$pptx}\">download powerpoint</a></p>";
