<?php
/**
 * @license     Unlicense <https://unlicense.org>
 * @author      Robin de Graaf <hello@devvoh.com>
 */

require_once('./src/devvoh/fixedwidth/Generator.php');
require_once('./src/devvoh/fixedwidth/Reader.php');
require_once('./src/devvoh/fixedwidth/Schema.php');
require_once('./src/devvoh/fixedwidth/Schema/Field.php');

$schemaData = new \Devvoh\FixedWidth\Schema;
$schemaData->setDelimiter(';');
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
$schemaOther->setDelimiter(';');
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

// We're going to fill dataLines with generated data
$dataLines     = [];
$dataArray     = [];
$dataArrayTrim = [];

$generatorData = new \Devvoh\FixedWidth\Generator;
$generatorData->setSchema($schemaData);
$generatorData->addData([ // addData takes an array of data and adds it to the stack
    'data'       => 'dataaaa', // trimmed at field length of 4 & uppercased due to callback
    'number_one' => '1',
    'comment'    => 'Hello there.',
    'number_two' => 41
]);

// This is an array of strings
$dataLines[]   = $generatorData->asString();
// This is an array with arrays merged into it, for a same-level array as a result
$dataArray     = $generatorData->asArray();
// Same as above, only all the values are trimmed
$dataArrayTrim = $generatorData->asArray(true);

$generatorOther = new \Devvoh\FixedWidth\Generator;
$generatorOther->setSchema($schemaOther);
// Now generate 3 lines of 'other' data
$generatorOther->setData([
    [
        'number_one' => '1236',
        'comment' => 'This comment',
        'bool1' => 'NO',
        'bool2' => 'Y',
        'bool3' => 'N',
        'bool4' => 'Y',
    ],
    [
        'number_one' => '452',
        'comment' => 'also comment',
        'bool1' => 'YE',
        'bool2' => 'N',
        'bool3' => 'N',
        'bool4' => 'Y',
    ],
    [
        'number_one' => '1643',
        'comment' => 'This comment',
        'bool1' => 'NO',
        'bool2' => 'Y',
        'bool3' => 'N',
        'bool4' => 'Y',
    ],
]);
$dataLines[]   = $generatorOther->asString();
$dataArray     = array_merge($dataArray, $generatorOther->asArray());
$dataArrayTrim = array_merge($dataArrayTrim, $generatorOther->asArray(true));

// And add another data line
$generatorData->clearData();
$generatorData->addData([ // addData takes an array of data and adds it to the stack
    'data'       => 'end', // trimmed at field length of 4 & uppercased due to callback
    'number_one' => '207',
    'comment'    => 'Hello there again.',
    'number_two' => 41
]);
$dataLines[]   = $generatorData->asString();
$dataArray     = array_merge($dataArray, $generatorData->asArray());
$dataArrayTrim = array_merge($dataArrayTrim, $generatorOther->asArray(true));

// Now implode the array of strings & json
$dataString     = implode(PHP_EOL, $dataLines);

echo '<pre>' . $dataString;

echo '<hr>';

var_dump($dataArray);

echo '<hr>';

var_dump($dataArrayTrim);

echo '</pre>';