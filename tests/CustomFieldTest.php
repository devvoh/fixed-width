<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests;

use Devvoh\FixedWidth\CustomField;
use Devvoh\FixedWidth\PadPlacement;
use PHPUnit\Framework\TestCase;

class CustomFieldTest extends TestCase
{
    public function testBasicCustomFieldCreation(): void
    {
        $field = new CustomField('id', 10);

        self::assertSame('id', $field->getKey());
        self::assertSame(10, $field->getLength());
    }

    public function testAdvancedCustomFieldCreation(): void
    {
        $field = new CustomField(
            'id',
            10,
            '0',
            PadPlacement::LEFT(),
            '0123456789',
            function (string $value) {
                return (int)$value;
            },
            function ($value): string {
                return (string)$value;
            }
        );

        self::assertSame('id', $field->getKey());
        self::assertSame(10, $field->getLength());
        self::assertSame('0', $field->getPadCharacter());
        self::assertTrue($field->getPadPlacement()->equals(PadPlacement::LEFT()));
        self::assertSame('0123456789', $field->getAllowedCharacters());

        self::assertSame(1, $field->transform('1'));
        self::assertSame('1', $field->unTransform(1));
    }
}
