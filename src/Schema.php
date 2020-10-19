<?php declare(strict_types=1);

namespace Devvoh\FixedWidth;

class Schema
{
    /** @var Field[] */
    private $fields = [];

    /** @var string|null */
    private $delimiter;

    public function __construct(Field ...$fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    public function getDelimiter(): ?string
    {
        return $this->delimiter;
    }

    public function addField(Field $field): void
    {
        $this->fields[$field->getKey()] = $field;
    }

    public function getField(string $key): ?Field
    {
        return $this->fields[$key] ?? null;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getLineLength(): int
    {
        $lineLength = 0;

        foreach ($this->fields as $field) {
            $lineLength += $field->getLength();
        }

        if ($this->delimiter !== null) {
            $lineLength += count($this->fields) * strlen($this->delimiter);
        }

        return $lineLength;
    }
}
