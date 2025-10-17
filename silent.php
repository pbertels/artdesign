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

$W = 297;
$H = 210;
$MARGIN = 15;
$MARGIN_TOP = 10;
$WIDTH = $W - 2 * $MARGIN;
$HEIGHT = $H - 2 * $MARGIN_TOP;
$SPACER = 10;
$IMAGE = 25;
$timestamp = date('Ymd-Hi');

$silent = new PdfCatalog($W, $H);
$silent->SetMargins($MARGIN, $MARGIN_TOP, null, true);
$silent->SetAutoPageBreak(false);

// PREPARE 
$FIELDS = [
    'Bod' => ['2cm', 'right'],
    'Uw biednummer' => ['3.5cm', 'left'],
    'Handtekening' => ['6.5cm', 'left'],
];
function prepareRow($FIELDS, $values = [])
{
    $rst = '<tr>';
    foreach ($FIELDS as $name => $field) {
        $value = $values == 'header' ? $name : (isset($values[$name]) ? $values[$name] : '<br><br><br>');
        $align = $values == 'header' ? 'left' : $field[1];
        $rst .= "<td width=\"{$field[0]}\" style=\"text-align: {$align}\">&nbsp;&nbsp;{$value}&nbsp;&nbsp;</td>";
    }
    $rst .= '</tr>';
    return $rst;
}

// LOOP
foreach ($art as $code => $artwork) {

    // DATA
    $lot = $artwork['Lot'];
    if ($lot >= 200) continue;

    // HEADER
    $silent->AddPage();
    $silent->setFont('anton', '', 16);
    $silent->setColorArray('text', $BLACK);
    $silent->setX(0);
    $silent->writeHTML("<h3>Lot {$lot}</h3>");
    $silent->setFont('anton', '', 24);
    $silent->setColorArray('text', $RED);
    $silent->setX(0);
    $silent->writeHTML("<h3>{$artwork['WerkHoofdlettersKorter']}</h3>");
    $silent->setColorArray('text', $GREEN);
    $silent->setFont('anton', '', 10);
    $silent->writeHTML("<h1>{$artwork['KunstDesignerAntonGreen']}</h1>");
    $silent->setColorArray('text', $BLACK);
    $silent->Ln(3);
    $silent->setFont('helvetica', '', 12);
    $silent->setColorArray('text', $BLACK);
    $silent->writeHTMLCell($W / 2 - $SPACER, 0, $MARGIN - 1, $silent->GetY(), "
<p>
Doe een bod door uw biednummer in te vullen, en uw handtekening te plaatsen naast het bedrag van uw keuze. 
We brengen de hoogste bieder op het einde van de veiling op de hoogte met e-mail en sms. Na betaling van het juiste bedrag, kan de winnende bieder het werk ophalen.
<br></p>
", 0, 1);

    // STEPS
    $ALTERNATIVE_STEPS = [
        100000000 => 1000,
        10000 => 1000,
        5000 => 500,
        4200 => 300,
        3800 => 200,
        3200 => 300,
        1000 => 250,
        500 => 100,
        200 => 50,
        100 => 50,
        50 => 20,
        0 => 50,
    ];

    // TABLE - print
    $bod = $artwork['Prijs'];
    $silent->setY(68.2);
    for ($column = 0; $column <= 1; $column++) {
        $silent->setFont('anton', '', 11);
        $html = '<table>' . prepareRow($FIELDS, 'header') . '</table>';
        $silent->writeHTML($html);
        $silent->Ln(-2);
        $html = '<table border="1px solid gray">';
        $i = 0;
        for ($i = 0; $i < ($column == 0 ? 6 : 9); $i++) {
            $html .= prepareRow($FIELDS, ['Bod' => "<br>" . euro($bod) . "&nbsp;"]);
            $bod = increment($bod, $ALTERNATIVE_STEPS);
        }
        $html .= '</table>';
        $silent->setFont('helvetica', '', 11);
        $silent->writeHTMLCell($W / 2, 0, $column * $W / 2 + (1 - $column) * $MARGIN + $column * $SPACER, $silent->GetY(), $html);
        $silent->setXY($W / 2 + $SPACER, $MARGIN_TOP);
    }
}

// OUTPUT
$silent->output("silent-{$timestamp}.pdf");
