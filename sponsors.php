<?php

$DOELEN = [
    'rodekruis' => [
        'name' => 'Rode Kruis',
        'desc' => 'Het ingezamelde geld gaat naar de fondsenwervingscampagne die Rode Kruis-Vlaanderen specifiek heeft opgezet naar aanleiding van het huidige conflict. De middelen worden uitsluitend ingezet in het kader van deze noodhulp, en toegewezen aan de Rode Kruis-actor die op het moment van overschrijving het best kan inspelen op de hoogste noden van de bevolking. Vaak gaat het om het Internationale Rode Kruis (ICRC) of de Palestijnse Rode Halve Maan. Zij bieden medische steun, voedsel en water, psychosociale zorg, hulp bij gezinshereniging en onderhandelingen op basis van hun neutrale rol.',
    ],
    'oxfam' => [
        'name' => 'Oxfam',
        'desc' => 'Oxfam is sinds de jaren 1950 actief in Gaza en de Westelijke Jordaanoever en werkt nauw samen met lokale partners. Hun teams zorgen voor drinkwater, voedselpakketten, cash-steun en bescherming. Bijzondere aandacht gaat naar vrouwen en kinderen, die toegang krijgen tot psychosociale, juridische en reproductieve gezondheidszorg, ondanks extreme tekorten en beperkingen.',
    ],
    'unrwa' => [
        'name' => 'UNRWA',
        'desc' => 'UNRWA (United Nations Relief and Works Agency) is sinds 1949 actief voor Palestijnse vluchtelingen en telt vandaag meer dan 12.000 medewerkers in Gaza. Het ingezamelde geld gaat rechtstreeks naar hun werking ter plaatse, waar ze via dat lokale netwerk onderdak, onderwijs, psychosociale hulp, medische zorg, drinkwater en sanitaire voorzieningen bieden aan duizenden gezinnen. Hun werk vormt letterlijk een levenslijn in onmenselijke omstandigheden.',
    ],

];
ksort($DOELEN);
foreach ($DOELEN as $code => $doel) {
    $DOELnames[] = $doel['name'];
}
sort($DOELnames);
$DOELENLIJST = implode(', ', array_slice($DOELnames, 0, -1)) . ' en ' . implode('', array_slice($DOELnames, -1, 1));


$SPONSORS = [
    'norsu' => ['name' => 'Norsu', 'desc' => 'drukwerk'],
    'brainsolutions' => ['name' => 'Brainsolutions', 'desc' => 'audiovisueel'],
    'verschueren' => ['name' => 'Drankencentrale Verschueren', 'desc' => 'drank'],
    // 'vanhees' => ['name' => 'Kantoor Van Hees', 'desc' => 'verzekeringen'],
    'gsv' => ['name' => 'GSV Verhuur', 'desc' => 'toiletten'],
    'opnieuwenco' => ['name' => 'Opnieuw & Co', 'desc' => 'vanalles'],
    'masjien' => ['name' => 'Masjien', 'desc' => 'drukwerk'],
    '3wilgen' => ['name' => 'De Drie Wilgen', 'desc' => 'drank'],
    'rentabar' => ['name' => 'Rent@Bar', 'desc' => 'Dennis van de foodtrucks'],
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
