<?php

namespace ArtDesign;

require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';

class PdfCatalog extends \TCPDF
{
    public function __construct()
    {
        // parent::__construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = true);
        parent::__construct('P', 'mm', [216,216], true, 'UTF-8', false, true);
        $this->SetDisplayMode($zoom = 'fullpage', $layout = 'TwoColumnRight', $mode = 'UseNone');
        $this->setViewerPreferences(array('Duplex' => 'DuplexFlipLongEdge'));
        $this->SetMargins(13, 13, null, true);
        $this->SetBooklet(true, 13, 23);
    }
    public function Header() {}
    public function Footer() {}
    public function writeHTMLCell($w, $h, $x, $y, $html = '', $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = '', $autopadding = true)
    {
        // $html = preg_replace('/<h([1234])>/', '<h\1 style="font-family: Montserrat">', $html);
        // $html = preg_replace('/<strong>/', '<strong style="font-family: Montserrat">', $html);
        return parent::writeHTMLCell($w, $h, $x, $y, $html, $border, $ln, $fill, $reseth, $align, $autopadding);
    }
}
