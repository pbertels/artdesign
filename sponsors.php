<?php

$SPONSORS = [
    'vinovatie' => ['name' => 'Vinovatie', 'desc' => 'Vinovatie is gespecialiseerd in rechtstreekse import van Spaanse kwaliteitswijnen & delicatessen - in een biodynamische sfeer - en verdeelt ze aan de betere horeca.'],
    'norsu' => ['name' => 'Norsu', 'desc' => ''],
    'masjien' => ['name' => 'Masjien', 'desc' => ''],
    '3wilgen' => ['name' => 'De Drie Wilgen', 'desc' => ''],
    'rentabar' => ['name' => 'Rentabar', 'desc' => 'Dennis'],
    'rest' => ['name' => 'REST Mortsel', 'desc' => 'workshop kinderen'],
    'edith' => ['name' => 'Edith Lafond', 'desc' => 'zus van'],
    'bonheur' => ['name' => 'Bonheur', 'desc' => ''],
    'yugen' => ['name' => 'Yugen', 'desc' => 'kombucha'],
    'kunstwerkt' => ['name' => 'Kunst Werkt', 'desc' => 'ezels'],
    // '' => ['name' => '', 'desc' => ''],
];
ksort($SPONSORS);

$SPONSORnames = [];
foreach ($SPONSORS as $code => $sponsor) {
    $SPONSORnames[] = $sponsor['name'];
}
sort($SPONSORnames);
$SPONSORLIST = implode(', ', array_slice($SPONSORnames, 0, -1)) . ' en ' . implode('', array_slice($SPONSORnames, -1, 1));
