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
$MARGIN = 15;
$WIDTH = $W - 2 * $MARGIN;
$HEIGHT = $H - 2 * $MARGIN;
$SPACER = 10;
$timestamp = date('Ymd-Hi');

$silent = new PdfCatalog($W, $H);
$silent->SetMargins($MARGIN, $MARGIN, null, true);
$silent->SetAutoPageBreak(false);

// PREPARE 
$FIELDS = [
    'Bod' => ['2cm', 'right'],
    'Naam' => ['7.5cm', 'left'],
    'Telefoon' => ['4cm', 'left'],
    'E-mail' => ['5cm', 'left'],
];
function prepareRow($FIELDS, $values = [])
{
    $rst = '<tr>';
    foreach ($FIELDS as $name => $field) {
        $value = $values == 'header' ? $name : (isset($values[$name]) ? $values[$name] : '<br><br>');
        $align = $values == 'header' ? 'left' : $field[1];
        $rst .= "<td width=\"{$field[0]}\" style=\"text-align: {$align}\">&nbsp;&nbsp;{$value}&nbsp;&nbsp;</td>";
    }
    $rst .= '</tr>';
    return $rst;
}

// LOOP
foreach ($art as $code => $artwork) {

    // DATA
    $work = strtoupper($artwork['Werk']);
    $work = str_replace(['é', 'â'], ['E', 'A'], $work);
    $artist = $artwork['KunstDesigner'];
    $lot = $artwork['Lot'];

    // HEADER
    $silent->AddPage();
    $silent->setFont('anton', '', 16);
    $silent->setColorArray('text', $BLACK);
    $silent->setX(0);
    $silent->writeHTML("<h3>Lot {$lot}</h3>");
    $silent->setFont('anton', '', 24);
    $silent->setColorArray('text', $RED);
    $silent->setX(0);
    $silent->writeHTML("<h3>{$work}</h3>");
    $silent->setColorArray('text', $GREEN);
    $silent->writeHTML("<h3>{$artist}</h3>");
    $silent->setColorArray('text', $BLACK);
    $silent->Ln(3);

    // TABLE - print
    $silent->setFont('anton', '', 11);
    $html = '<table>' . prepareRow($FIELDS, 'header') . '</table>';
    $silent->writeHTML($html);
    $silent->Ln(-2);
    $html = '<table border="1px solid gray">';
    $i = 0;
    $bod = start($artwork['Prijs']);
    for ($i = 0; $i < 15; $i++) {
        $html .= prepareRow($FIELDS, ['Bod' => "<br>" . euro($bod) . "&nbsp;"]);
        $bod = increment($bod);
    }
    $html .= '</table>';
    $silent->setFont('helvetica', '', 11);
    $silent->writeHTML($html);
}

// OUTPUT
$silent->output("silent-{$timestamp}.pdf");
