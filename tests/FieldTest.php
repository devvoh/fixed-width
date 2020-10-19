<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests;

use Devvoh\FixedWidth\PadPlacement;
use Devvoh\FixedWidth\Tests\Fields\IdField;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    public function testFieldCreation(): void
    {
        $field = new IdField();

        self::assertSame('id', $field->getKey());
        self::assertSame(10, $field->getLength());
        self::assertSame('0', $field->getPadCharacter());
        self::assertTrue(PadPlacement::LEFT()->equals($field->getPadPlacement()));
    }
}
