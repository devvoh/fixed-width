# Devvoh Fixed Width

[![Build Status](https://travis-ci.com/devvoh/fixed-width.svg?branch=master)](https://travis-ci.com/devvoh/fixed-width)
[![Latest Stable Version](https://poser.pugx.org/devvoh/fixed-width/v/stable)](https://packagist.org/packages/devvoh/fixed-width)
[![Latest Unstable Version](https://poser.pugx.org/devvoh/fixed-width/v/unstable)](https://packagist.org/packages/devvoh/fixed-width)
[![License](https://poser.pugx.org/devvoh/fixed-width/license)](https://packagist.org/packages/devvoh/fixed-width)


## Install

Php 7.2+ and [composer](https://getcomposer.org) are required.

```bash
$ composer require devvoh/fixed-width
```

## Usage

Devvoh Fixed Width is a simple library to both read and generate fixed width data files.

The intent is to set up a `Schema` with `Fields` once, and just reuse that for both reading and
generating files.

## Examples

Data example:
```text
0000000001devvoh              0a
0000000002test                1b
0000000003person              0c
0000000004also_person         1b
```

With `CustomField`:
```php
$reader = new Reader(new Schema(
    new CustomField('id', 10, '0', PadPlacement::LEFT()),
    new CustomField('username', 20, ' ', PadPlacement::RIGHT()),
    new CustomField('boolean', 1, null, null, '10'),
    new CustomField('restricted', 1, null, null, 'abc')
));

$data = $reader->readLines($dataAsString); // the data from above
```

The data returned would be an array of 4 arrays of the interpreted data:

```php
[
    [
        'id' => '1',
        'username' => 'devvoh',
        'boolean' => '0',
        'restricted' => 'a'
    ],
    ...
];
```

It's also possible to set up dedicated fields by creating a class which implements the `Field` interface:

```php
class IdField implements Field, SupportsPadding
{
    public function getKey(): string
    {
        return 'id';
    }

    public function getLength(): int
    {
        return 10;
    }

    public function getPadCharacter(): string
    {
        return '0';
    }

    public function getPadPlacement(): PadPlacement
    {
        return PadPlacement::LEFT();
    }
}
```

To use these, you simply pass this instead of a new `CustomField` instance, and can be freely combined with custom fields:

```php
new Schema(
    new IdField(),
    new CustomField('username', 20, ' ', PadPlacement::RIGHT())
);
```

This allows both concretely implemented classes to live in your code, to be reused if needed, or to store the schema
as some other data structure somewhere and create them on the fly using the `CustomField` implementation.

To generate from data to a newline-separated string, it's almost exactly the opposite of the `Reader` logic above.

```php
$generator = new Generator(new Schema(
    new CustomField('id', 10, '0', PadPlacement::LEFT()),
    new CustomField('username', 20, ' ', PadPlacement::RIGHT()),
    new CustomField('boolean', 1, null, null, '10'),
    new CustomField('restricted', 1, null, null, 'abc')
));

$generator->setData([
    [
        'id' => '1',
        'username' => 'devvoh',
        'boolean' => '0',
        'restricted' => 'a'
    ]
]);

$generator->toString();
```

The output would be, as expected:

```text
0000000001devvoh              0a
```

Both `Generator` and `Reader` can deal with a `Schema` that has a delimiter. This is a rarer case, but it can happen.

```php
$schema = new Schema(
    new IdField(),
    new UsernameField()
);
$schema->setDelimiter(';');

$generator = new Generator($schema);

$generator->toString();
```

Would output:

```text
0000000001;devvoh              ;
```

And if the same `Schema` is used to create a new `Reader`, the reader will require and interpret the delimiter properly.
