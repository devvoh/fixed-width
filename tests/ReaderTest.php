<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests;

use Devvoh\FixedWidth\CustomField;
use Devvoh\FixedWidth\PadPlacement;
use Devvoh\FixedWidth\Reader;
use Devvoh\FixedWidth\Schema;
use Devvoh\FixedWidth\Tests\Fields\BooleanField;
use Devvoh\FixedWidth\Tests\Fields\CenteredField;
use Devvoh\FixedWidth\Tests\Fields\IdField;
use Devvoh\FixedWidth\Tests\Fields\RestrictedField;
use Devvoh\FixedWidth\Tests\Fields\UsernameField;
use LogicException;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    /** @var Schema */
    private $schema;

    /** @var Schema */
    private $schemaWithDelimiter;

    public function setUp()
    {
        parent::setUp();

        $this->schema = new Schema(
            new IdField(),
            new UsernameField(),
            new BooleanField(),
            new RestrictedField()
        );

        $this->schemaWithDelimiter = new Schema(
            new IdField(),
            new UsernameField(),
            new BooleanField(),
            new RestrictedField()
        );
        $this->schemaWithDelimiter->setDelimiter(';');
    }

    public function testReaderCanReadLineAndHandlesFieldsAppropriately(): void
    {
        $reader = new Reader($this->schema);

        self::assertSame(
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a'
            ],
            $reader->readLine('0000000001devvoh              0a')
        );
    }

    public function testReaderCanReadLineAndHandlesFieldsAppropriatelyWithCustomFields(): void
    {
        $integerTransformer = function (string $value) {
            return (int)$value;
        };
        $integerUnTransformer = function ($value): string {
            return (string)$value;
        };

        $booleanTransformer = function (string $value) {
            return $value === '1';
        };
        $booleanUnTransformer = function ($value): string {
            return $value ? '1' : '0';
        };

        $reader = new Reader(new Schema(
            new CustomField('id', 10, '0', PadPlacement::LEFT(), null, $integerTransformer, $integerUnTransformer),
            new CustomField('username', 20, ' ', PadPlacement::RIGHT()),
            new CustomField('boolean', 1, null, null, '10', $booleanTransformer, $booleanUnTransformer),
            new CustomField('restricted', 1, null, null, 'abc')
        ));

        self::assertSame(
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a'
            ],
            $reader->readLine('0000000001devvoh              0a')
        );
    }

    public function testReaderDoesntLikeInvalidLineLength(): void
    {
        $reader = new Reader($this->schema);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Line is not the right length, got 54, expected 32');

        $reader->readLine('this is way too long, hahahahaha, but what can you do?');
    }

    public function testReaderCanReadMultipleLines(): void
    {
        $reader = new Reader($this->schema);

        $data = $reader->readLines(implode(
            PHP_EOL,
            [
                '0000000001devvoh              0a',
                '0000000002test                1b',
                '0000000003person              0c',
                '0000000004also_person         1b',
            ]
        ));

        self::assertSame(
            [
                [
                    'id' => 1,
                    'username' => 'devvoh',
                    'boolean' => false,
                    'restricted' => 'a'
                ],
                [
                    'id' => 2,
                    'username' => 'test',
                    'boolean' => true,
                    'restricted' => 'b'
                ],
                [
                    'id' => 3,
                    'username' => 'person',
                    'boolean' => false,
                    'restricted' => 'c'
                ],
                [
                    'id' => 4,
                    'username' => 'also_person',
                    'boolean' => true,
                    'restricted' => 'b'
                ],
            ],
            $data
        );
    }

    public function testReaderCanInterpretDelimiter(): void
    {
        $reader = new Reader($this->schemaWithDelimiter);

        self::assertSame(
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a'
            ],
            $reader->readLine('0000000001;devvoh              ;0;a;')
        );
    }

    public function testReaderRequiresDelimiterIfSet(): void
    {
        $reader = new Reader($this->schemaWithDelimiter);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Line does not contain delimiter as set in schema: ;');

        $reader->readLine('0000000001devvoh              0a    ');
    }

    public function testReaderUnderstandsAllPadding(): void
    {
        $reader = new Reader(new Schema(
            new IdField(), // left-padded '0'
            new UsernameField(), // right-padded ' '
            new CenteredField() // center-padded '.'
        ));

        self::assertSame(
            [
                'id' => 1,
                'username' => 'devvoh',
                'centered' => 'XX'
            ],
            $reader->readLine('0000000001devvoh              ....XX....')
        );
    }

    public function testReaderThrowsExceptionIfFieldHasDisallowedCharacter(): void
    {
        $reader = new Reader(new Schema(
            new RestrictedField()
        ));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Field 'restricted' has invalid characters. Allowed: abc, provided: x");

        $reader->readLine('x');
    }
}
