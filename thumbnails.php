<?php

$SCALE = 0.4;

foreach (glob('./images/*.jpg') as $filename) {
    list($width, $height) = getimagesize($filename);
    $newwidth = round($width * $SCALE);
    $newheight = round($height * $SCALE);
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    $source = imagecreatefromjpeg($filename);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    $basename = substr($filename, 9);
    imagejpeg($thumb, "./images/thumbnails/{$basename}", 40);
}
