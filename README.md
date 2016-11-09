## Fixed Width File Reader & Generator

[![Latest Stable Version](https://poser.pugx.org/devvoh/fixed-width/v/stable)](https://packagist.org/packages/devvoh/fixed-width)
[![Latest Unstable Version](https://poser.pugx.org/devvoh/fixed-width/v/unstable)](https://packagist.org/packages/devvoh/fixed-width)
[![License](https://poser.pugx.org/devvoh/fixed-width/license)](https://packagist.org/packages/devvoh/fixed-width)

Fixed-width is a simple library that can make working with fixed width file formats much easier. You define a Schema for a line, which can have a delimiter or not, and both the Reader and Generator will work with that Schema.

You can define valid characters, and the line or data will be rejected if it doesn't adhere to it. If a delimiter is found in a data value, it's rejected. If a line is rejected, the `Reader` will return `null`, which you can then interpret as you wish. If a dataItem is rejected, the `Generator` will return `-- invalid data: data`.

## Requirements

- PHP 5.6, PHP 7

## Installation

Fixed-width can be installed by using [Composer](http://getcomposer.org/). Simply run:

`composer require devvoh/fixedwidth`

## License

Fixed-width is open-sourced software licensed under the [Unlicense](http://unlicense.org/).