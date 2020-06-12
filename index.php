<?php

/*$data = file_get_contents('https://obmenka.kharkov.ua/');

if (preg_match('#WUJS\.Load\(\'app-container\', (.*?), formComponent\);#', $data, $matches)) {
    $dataStripped = json_decode($matches[1], true);
    $datetime = $dataStripped ['datetime'];
    $infoAr = [];
    foreach ($dataStripped ['rates'] as $rate) {
        $infoAr [] = $rate ['currencyBase'] . '-' . $rate ['currencyQuoted'] . ' : ' . $rate ['rateBid'] . ' / ' . $rate ['rateAsk'];
    }
    echo 'Дата: ' . $datetime . PHP_EOL . implode(PHP_EOL, $infoAr);
}*/
/*$dataFull = file_get_contents('https://www.worldometers.info/coronavirus/country/ukraine/');

preg_match('#' .
    preg_quote('<div style="font-size:13px; color:#999; text-align:center">', '#') .
    'Last updated: (.*?)<\/div>' .
    '.*' .
    '<span style="color\:\#aaa">(.*?) <\/span>' .
    '.*' .
    '<span>(.*?)<\/span>' .
    '.*' .
    '<span>(.*?)<\/span>' .
    '#is',
    $dataFull, $matches);

$dateTime = new \DateTime($matches[1]);
$dateTime->setTimezone(new DateTimeZone('Europe/Kiev'));


echo $dateTime->format('d.m.Y H:i:s');*/

/*$datetime = strtotime($matches[1]);
var_dump($datetime);
echo date('d.m.Y H:i:s', $datetime);*/

/*$dataFull = file_get_contents('https://en.wikipedia.org/wiki/2020_coronavirus_pandemic_in_Ukraine');

preg_match('#' .
    'The following information was reported as of (.*?) on (.*?):' .
    '.+' .
    'Kharkiv Oblast</a>\s</td>' .
    // '.*' .
    '\s*<td align="center">(.*?)\s</td>' .
    // '.*' .
    '\s<td align\="center">(.*?)\s<\/td>' .
    // '.*' .
    '\s<td align\="center">(.*?)\s<\/td>' .
    '#uis',
    $dataFull, $matches);
var_dump($matches);*/

// var_dump(new \DateTime('27 April 2020 09:00', new \DateTimeZone('Europe/Kiev')));

$dataFull = file_get_contents('https://www.anekdot.ru/random/anekdot/');
preg_match_all('#' .
    '<div class="text">(.*?)</div>' .
    '#uis', $dataFull, $matches);

$textAr = [];
if (!empty($matches[1])) {
    foreach ($matches[1] as $match) {
        $textAr [] = str_replace(['<br>', '&quot;'], [PHP_EOL, '"'], $match);
    }
}

var_dump($matches);

