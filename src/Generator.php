<?php declare(strict_types=1);

namespace Devvoh\FixedWidth;

use Devvoh\FixedWidth\Field\SupportsAllowedCharacters;
use Devvoh\FixedWidth\Field\SupportsPadding;
use Devvoh\FixedWidth\Field\SupportsTransforming;
use LogicException;

class Generator
{
    /** @var Schema */
    private $schema;

    /** @var array */
    private $data = [];

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(array $item): void
    {
        $this->data[] = $item;
    }

    public function toString(): string
    {
        $string = '';

        foreach ($this->toArray() as $item) {
            $string .= sprintf(
                '%s%s%s',
                implode($this->schema->getDelimiter(), $item),
                $this->schema->getDelimiter(),
                PHP_EOL
            );
        }

        return trim($string);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        $transformedItems = [];

        foreach ($this->data as $item) {
            foreach ($this->schema->getFields() as $field) {
                $value = $item[$field->getKey()] ?? null;

                if ($value === null) {
                    throw new LogicException(
                        sprintf(
                            "Field '%s' not found in data item",
                            $field->getKey()
                        )
                    );
                }

                $value = (string)$value;

                if ($field instanceof SupportsTransforming) {
                    $value = $field->unTransform($value);
                }

                if (strlen($value) > $field->getLength()) {
                    throw new LogicException(
                        sprintf(
                            "Field '%s' is longer than length %d, actually %d",
                            $field->getKey(),
                            $field->getLength(),
                            strlen($value)
                        )
                    );
                }

                if ($field instanceof SupportsPadding) {
                    $value = str_pad(
                        $value,
                        $field->getLength(),
                        $field->getPadCharacter(),
                        $field->getPadPlacement()->getValue()
                    );
                }

                if ($field instanceof SupportsAllowedCharacters && $field->getAllowedCharacters() !== null) {
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

                $item[$field->getKey()] = $value;
            }

            $transformedItems[] = $item;
        }

        return $transformedItems;
    }
}
