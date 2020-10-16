<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests;

use Devvoh\FixedWidth\Field;
use Devvoh\FixedWidth\SupportsAllowedCharacters;
use Devvoh\FixedWidth\PadPlacement;
use Devvoh\FixedWidth\Schema;
use Devvoh\FixedWidth\Tests\Fields\BooleanField;
use Devvoh\FixedWidth\Tests\Fields\IdField;
use Devvoh\FixedWidth\Tests\Fields\UsernameField;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    public function testSchemaCreation(): void
    {
        $schema = new Schema();

        self::assertInstanceOf(Schema::class, $schema);
        self::assertEmpty($schema->getFields());
        self::assertSame(0, $schema->getLineLength());
    }

    public function testSchemaCreationWithFields(): void
    {
        $schema = new Schema(new IdField(), new UsernameField(), new BooleanField());

        self::assertCount(3, $schema->getFields());
        self::assertSame(31, $schema->getLineLength());
    }

    public function testSchemaAddField(): void
    {
        $schema = new Schema();

        self::assertEmpty($schema->getFields());
        self::assertSame(0, $schema->getLineLength());

        $schema->addField(new IdField());

        self::assertCount(1, $schema->getFields());
        self::assertSame(10, $schema->getLineLength());
    }

    public function testSchemaGetFieldReturnsNullIfNotExists(): void
    {
        $schema = new Schema();

        self::assertNull($schema->getField('id'));
    }

    public function testSchemaGetFieldReturnsField(): void
    {
        $schema = new Schema(new IdField());

        $field = $schema->getField('id');

        self::assertInstanceOf(Field::class, $field);
        self::assertSame(10, $field->getLength());
    }
}
