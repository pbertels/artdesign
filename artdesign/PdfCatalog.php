<?php

namespace ArtDesign;

require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';

class PdfCatalog extends \TCPDF
{
    private $wq = -1;
    private $hq = -1;
    public function __construct($w, $h)
    {
        parent::__construct($w > $h ? 'L' : 'P', 'mm', [$w, $h], true, 'UTF-8', false, true);
        $this->wq = $w;
        $this->hq = $h;
        $this->SetDisplayMode($zoom = 'fullpage', $layout = 'TwoColumnRight', $mode = 'UseNone');
        $this->setViewerPreferences(array('Duplex' => 'DuplexFlipLongEdge'));
    }
    public function Header() {}
    public function Footer() {}
    public function writeHTMLCell($w, $h, $x, $y, $html = '', $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = '', $autopadding = true)
    {
        // $html = preg_replace('/<h([1234])>/', '<h\1 style="font-family: Montserrat">', $html);
        // $html = preg_replace('/<strong>/', '<strong style="font-family: Montserrat">', $html);
        return parent::writeHTMLCell($w, $h, $x, $y, $html, $border, $ln, $fill, $reseth, $align, $autopadding);
    }
    public function AddSectionPage($title, $colour, $textColour, $width, $leftOdd)
    {
        $this->setFont('anton', '', 10);
        $this->AddPage();
        $this->setColorArray('text', $colour);
        $this->Rect(0, 0, $this->wq, $this->hq, 'F', [], $colour);
        $this->setTextColorArray($textColour);
        $titleUpper = strtoupper($title);
        $this->writeHTMLCell($width, 0, $leftOdd, $this->hq * 0.5, "<h1 style=\"font-size: 700%\">{$titleUpper}</h1>", 0, 1, false, true, 'R', false);
    }
}
