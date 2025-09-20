<?php

$SPONSORS = [
    'vinovatie' => ['name' => 'Vinovatie', 'desc' => 'Vinovatie is gespecialiseerd in rechtstreekse import van Spaanse kwaliteitswijnen & delicatessen - in een biodynamische sfeer - en verdeelt ze aan de betere horeca.'],
    'norsu' => ['name' => 'Norsu', 'desc' => ''],
    'masjien' => ['name' => 'Masjien', 'desc' => ''],
    '3wilgen' => ['name' => 'De Drie Wilgen', 'desc' => ''],
    'rest' => ['name' => 'REST Mortsel', 'desc' => ''],
    'edith' => ['name' => 'Edith Lafond', 'desc' => ''],
    'bonheur' => ['name' => 'Bonheur', 'desc' => ''],
    'mortselarij' => ['name' => 'De Mortselarij', 'desc' => ''],
    'rentabar' => ['name' => 'Rentabar', 'desc' => ''],
    // '' => ['name' => '', 'desc' => ''],
];

$SPONSORnames = [];
foreach ($SPONSORS as $code => $sponsor) {
    $SPONSORnames[] = $sponsor['name'];
}
sort($SPONSORnames);
$SPONSORLIST = implode(', ', array_slice($SPONSORnames, 0, -1)) . ' en ' . implode('', array_slice($SPONSORnames, -1, 1));