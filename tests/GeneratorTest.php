<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests;

use Devvoh\FixedWidth\CustomField;
use Devvoh\FixedWidth\Generator;
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

class GeneratorTest extends TestCase
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

    public function testGeneratorWithoutDelimiterToArray(): void
    {
        $generator = new Generator($this->schema);

        $generator->setData([
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a',
            ],
            [
                'id' => 2,
                'username' => 'test',
                'boolean' => true,
                'restricted' => 'a',
            ]
        ]);

        self::assertSame(
            [
                [
                    'id' => '0000000001',
                    'username' => 'devvoh              ',
                    'boolean' => '0',
                    'restricted' => 'a',
                ],
                [
                    'id' => '0000000002',
                    'username' => 'test                ',
                    'boolean' => '1',
                    'restricted' => 'a',
                ],
            ],
            $generator->toArray()
        );
    }

    public function testGeneratorWithoutDelimiterToJson(): void
    {
        $generator = new Generator($this->schema);

        $generator->setData([
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a',
            ],
            [
                'id' => 2,
                'username' => 'test',
                'boolean' => true,
                'restricted' => 'a',
            ]
        ]);

        self::assertSame(
            '[{"id":"0000000001","username":"devvoh              ","boolean":"0","restricted":"a"},{"id":"0000000002","username":"test                ","boolean":"1","restricted":"a"}]',
            $generator->toJson()
        );
    }

    public function testGeneratorWithoutDelimiterToString(): void
    {
        $generator = new Generator($this->schema);

        $generator->setData([
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a',
            ],
            [
                'id' => 2,
                'username' => 'test',
                'boolean' => true,
                'restricted' => 'a',
            ]
        ]);

        self::assertSame(
            implode(
                PHP_EOL,
                [
                    '0000000001devvoh              0a',
                    '0000000002test                1a',
                ]
            ),
            $generator->toString()
        );
    }

    public function testGeneratorToStringHandlesCustomFields(): void
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

        $generator = new Generator(new Schema(
            new CustomField('id', 10, '0', PadPlacement::LEFT(), null, $integerTransformer, $integerUnTransformer),
            new CustomField('username', 20, ' ', PadPlacement::RIGHT()),
            new CustomField('boolean', 1, null, null, '10', $booleanTransformer, $booleanUnTransformer),
            new CustomField('restricted', 1, null, null, 'abc')
        ));

        $generator->setData([
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a'
            ],
        ]);

        self::assertSame(
            '0000000001devvoh              0a',
            $generator->toString()
        );
    }

    public function testGeneratorDoesntLikeItWhenFieldsAreMissing(): void
    {
        $generator = new Generator($this->schema);

        $generator->setData([
            [
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a',
            ]
        ]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Field 'id' not found in data item");

        $generator->toString();
    }

    public function testGeneratorDisallowsValuesThatAreTooLong(): void
    {
        $generator = new Generator($this->schema);

        $generator->setData([
            [
                'id' => 12345678901,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a',
            ]
        ]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Field 'id' is longer than length 10, actually 11");

        $generator->toString();
    }

    public function testGeneratorChecksForAllowedCharacters(): void
    {
        $generator = new Generator($this->schema);

        $generator->addData(
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a',
            ]
        );

        self::assertSame(
            '0000000001devvoh              0a',
            $generator->toString()
        );

        $generator->addData(
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'q',
            ]
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Field 'restricted' has invalid characters. Allowed: abc, provided: q");

        $generator->toString();
    }

    public function testGeneratorUsesDelimiter(): void
    {
        $generator = new Generator($this->schemaWithDelimiter);

        $generator->addData(
            [
                'id' => 1,
                'username' => 'devvoh',
                'boolean' => false,
                'restricted' => 'a',
            ]
        );

        self::assertSame(
            '0000000001;devvoh              ;0;a;',
            $generator->toString()
        );
    }

    public function testReaderUnderstandsAllPadding(): void
    {
        $generator = new Generator(new Schema(
            new IdField(), // left-padded '0'
            new UsernameField(), // right-padded ' '
            new CenteredField() // center-padded '.'
        ));

        $generator->setData([
            [
                'id' => 1,
                'username' => 'devvoh',
                'centered' => 'XX'
            ]
        ]);

        self::assertSame(
            '0000000001devvoh              ....XX....',
            $generator->toString()
        );
    }
}
