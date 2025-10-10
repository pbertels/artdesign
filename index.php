<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/header.php';

echo '<h1>Art &amp; Design for Palestine</h1>';
echo '<p>Drukwerk catalogus: <a href="./download.php?type=binnenwerk&compress=compress">binnenwerk LR</a> of <a href="./download.php?type=binnenwerk">HR</a> en <a href="./download.php?type=kaft">kaft</a></p>';
echo '<p>Catalogus voor digitaal gebruik (kaft + binnenwerk samen): <a href="./download.php">catalogus.pdf</a></p>';
echo '<p>Sponsors: <a href="./download.php?type=sponsors">allemaal op 1 A3</a></p>';
echo '<p>Powerpoint met presentatie van alle werken: <a href="./powerpoint.php">aanmaken</a></p>';
echo '<p>Silent Auction: <a href="./silent.php">download A4</a></p>';
echo '<p>Overzicht: <a href="./overview.php">overzicht op A4</a></p>';
echo '<p>Single Source Of Truth: <a href="./proper.tsv">TSV data file</a></p>';
