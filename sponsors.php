<?php

$SPONSORS = [
    'vinovatie' => ['name' => 'Vinovatie', 'desc' => 'wijenn'],
    'norsu' => ['name' => 'Norsu', 'desc' => 'drukwerk'],
    'masjien' => ['name' => 'Masjien', 'desc' => 'drukwerk'],
    '3wilgen' => ['name' => 'De Drie Wilgen', 'desc' => 'drank'],
    'rentabar' => ['name' => 'Rentabar', 'desc' => 'Dennis van de foodtrucks'],
    'rest' => ['name' => 'REST Mortsel', 'desc' => 'workshop voor kinderen'],
    'edith' => ['name' => 'Edith Lafond', 'logo' => false, 'desc' => 'zus van'],
    'bonheur' => ['name' => 'Bonheur', 'desc' => ''],
    'yugen' => ['name' => 'Yugen', 'desc' => 'kombucha'],
    'kunstwerkt' => ['name' => 'Kunst Werkt', 'desc' => 'schildersezels'],
    // '' => ['name' => '', 'desc' => ''],
];
ksort($SPONSORS);

$SPONSORnames = [];
foreach ($SPONSORS as $code => $sponsor) {
    $SPONSORnames[] = $sponsor['name'];
}
sort($SPONSORnames);
$SPONSORLIST = implode(', ', array_slice($SPONSORnames, 0, -1)) . ' en ' . implode('', array_slice($SPONSORnames, -1, 1));
