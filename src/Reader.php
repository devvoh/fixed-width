<?php declare(strict_types=1);

namespace Devvoh\FixedWidth;

use Devvoh\FixedWidth\Field\FieldWithAllowedCharacters;
use Devvoh\FixedWidth\Field\FieldWithPadding;
use Devvoh\FixedWidth\Field\FieldWithTransforming;
use LogicException;

class Reader
{
    /** @var Schema */
    private $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function readLine(string $line): array
    {
        if (strlen($line) !== $this->schema->getLineLength()) {
            throw new LogicException(
                sprintf(
                    "Line is not the right length, got %d, expected %d",
                    strlen($line),
                    $this->schema->getLineLength()
                )
            );
        }

        if ($this->schema->getDelimiter() !== null
            && strpos($line, $this->schema->getDelimiter()) === false
        ) {
            throw new LogicException(
                sprintf(
                    "Line does not contain delimiter as set in schema: %s",
                    $this->schema->getDelimiter()
                )
            );
        }

        $values = [];
        $lastPosition = 0;

        foreach ($this->schema->getFields() as $field) {
            $value = substr($line, $lastPosition, $field->getLength());

            $lastPosition += $field->getLength();

            if ($this->schema->getDelimiter() !== null) {
                $value = str_replace('', $this->schema->getDelimiter(), $value);

                $lastPosition += strlen($this->schema->getDelimiter());
            }

            $value = trim($value);

            if ($field instanceof FieldWithPadding) {
                switch ($field->getPadPlacement()) {
                    case PadPlacement::LEFT():
                        $value = ltrim($value, $field->getPadCharacter());
                        break;

                    case PadPlacement::RIGHT():
                        $value = rtrim($value, $field->getPadCharacter());
                        break;

                    case PadPlacement::BOTH():
                        $value = trim($value, $field->getPadCharacter());
                        break;
                }
            }

            if ($field instanceof FieldWithAllowedCharacters && $field->getAllowedCharacters() !== null) {
                $invalidCharacters = array_diff(
                    str_split($value),
                    str_split($field->getAllowedCharacters())
                );

                if ($invalidCharacters !== []) {
                    throw new LogicException(
                        sprintf(
                            "Field '%s' has invalid characters. Allowed: %s, provided: %s",
                            $field->getKey(),
                            $field->getAllowedCharacters(),
                            $value
                        )
                    );
                }
            }

            if ($field instanceof FieldWithTransforming) {
                $value = $field->transform($value);
            }

            $values[$field->getKey()] = $value;
        }

        return $values;
    }

    public function readLines(string $lines): array
    {
        $linesExploded = explode(PHP_EOL, $lines);

        $valuesPerLine = [];

        foreach ($linesExploded as $line) {
            $valuesPerLine[] = $this->readLine($line);
        }

        return $valuesPerLine;
    }
}
