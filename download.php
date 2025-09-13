<?php

use ArtDesign\PdfCatalog;

require __DIR__ . '/vendor/autoload.php';

$timestamp = date('Ymd-Hi');

$catalog = new PdfCatalog();
$catalog->AddPage();
$catalog->writeHTMLCell(190, 40, 13, 13, '<h1>Art &amp; Design</h1>', 'LTR', 1, false, true, 'C', false);
$catalog->output("catalog-{$timestamp}.pdf");
