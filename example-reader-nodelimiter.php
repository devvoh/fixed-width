<?php
/**
 * @license     Unlicense <https://unlicense.org>
 * @author      Robin de Graaf <hello@devvoh.com>
 */

require_once('./src/devvoh/fixedwidth/Generator.php');
require_once('./src/devvoh/fixedwidth/Reader.php');
require_once('./src/devvoh/fixedwidth/Schema.php');
require_once('./src/devvoh/fixedwidth/Schema/Field.php');

$data = file_get_contents('./example-nodelimiter.txt');

$schemaData = new \Devvoh\FixedWidth\Schema;
$schemaData->setFields([
    [
        'key' => 'data',
        'length' => 4,
        'callback' => function($value) { return strtoupper($value); },
    ],
    [
        'key' => 'number_one',
        'length' => 10,
        'type' => 'numeric', // alphanumeric is the default, numeric uses left-padded 0
    ],
    [
        'key' => 'comment',
        'length' => 32,
    ],
    [
        'key' => 'number_two',
        'length' => 3,
        'padCharacter' => 0, // Using padCharacter & padPlacement does the same as setting type=numeric in this case,
        'padPlacement' => STR_PAD_LEFT, // but you can use your own logic here.
    ],
]);

$schemaOther = new \Devvoh\FixedWidth\Schema;
$schemaOther->setFields([
    [
        'key' => 'number_one',
        'length' => 10,
        'type' => 'numeric',
    ],
    [
        'key' => 'comment',
        'length' => 5,
    ],
    [
        'key' => 'bool1',
        'length' => 2,
        'validCharacters' => ['YE', 'NO'], // This will reject anything that isn't specifically passed on.
        'callback' => function($value) { return strtoupper($value); },
    ],
    [
        'key' => 'bool2',
        'length' => 1,
        'validCharacters' => ['Y', 'N'],
        'callback' => function($value) { return strtoupper($value); },
    ],
    [
        'key' => 'bool3',
        'length' => 1,
        'validCharacters' => ['Y', 'N'],
        'callback' => function($value) { return strtoupper($value); },
    ],
    [
        'key' => 'bool4',
        'length' => 1,
        'validCharacters' => ['Y', 'N'],
        'callback' => function($value) { return strtoupper($value); },
    ],
]);

$readerData = new \Devvoh\FixedWidth\Reader();
$readerData->setSchema($schemaData);

$readerOther = new \Devvoh\FixedWidth\Reader();
$readerOther->setSchema($schemaOther);

$dataArray = explode(PHP_EOL, $data);

$resultArray = [];
foreach ($dataArray as $line) {
    if ($data = $readerData->readLine($line)) {
        $resultArray[] = $data;
        continue;
    }
    if ($data = $readerOther->readLine($line)) {
        $resultArray[] = $data;
        continue;
    }
    $resultArray[] = '-- incompatible line';
}
echo '<pre>';

/*
 * Because we have a file without a delimiter, we cannot automatically recognize by delimiter that line 3
 * is quite certainly invalid data. However, by using validCharacters, we can see if expected data is where it needs
 * to be. That's how we still know it's invalid. Line 4 is invalid because 1 is not either Y or N.
 */

var_dump($resultArray);