<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests;

use Devvoh\FixedWidth\Generator;
use Devvoh\FixedWidth\Reader;
use Devvoh\FixedWidth\Schema;
use Devvoh\FixedWidth\Tests\Fields\BooleanField;
use Devvoh\FixedWidth\Tests\Fields\IdField;
use Devvoh\FixedWidth\Tests\Fields\RestrictedField;
use Devvoh\FixedWidth\Tests\Fields\UsernameField;
use LogicException;
use PHPUnit\Framework\TestCase;

class CombinedTest extends TestCase
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

    public function testReaderCanReadGeneratorOutput(): void
    {
        $generator = new Generator($this->schemaWithDelimiter);
        $reader = new Reader($this->schemaWithDelimiter);

        $data = [
            'id' => 1,
            'username' => 'devvoh',
            'boolean' => false,
            'restricted' => 'a',
        ];

        $generator->addData($data);

        $newData = $reader->readLine($generator->toString());

        self::assertSame(
            $data,
            $newData
        );
    }

    public function testGeneratorCanGenerateFromReaderOutput(): void
    {
        $generator = new Generator($this->schemaWithDelimiter);
        $reader = new Reader($this->schemaWithDelimiter);

        $data = '0000000001;devvoh              ;0;a;';

        $generator->addData($reader->readLine($data));

        self::assertSame(
            $data,
            $generator->toString()
        );
    }
}
