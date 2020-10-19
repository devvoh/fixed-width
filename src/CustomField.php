<?php declare(strict_types=1);

namespace Devvoh\FixedWidth;

use Devvoh\FixedWidth\Field\FieldWithAllowedCharacters;
use Devvoh\FixedWidth\Field\FieldWithPadding;
use Devvoh\FixedWidth\Field\FieldWithTransforming;

class CustomField implements Field, FieldWithAllowedCharacters, FieldWithTransforming, FieldWithPadding
{
    /** @var string */
    private $key;

    /** @var int */
    private $length;

    /** @var string|null */
    private $padCharacter;

    /** @var PadPlacement|null */
    private $padPlacement;

    /** @var string|null */
    private $allowedCharacters;

    /** @var callable|null */
    private $transformer;

    /** @var callable|null */
    private $unTransformer;

    public function __construct(
        string $key,
        int $length,
        ?string $padCharacter = null,
        ?PadPlacement $padPlacement = null,
        ?string $allowedCharacters = null,
        ?callable $transformer = null,
        ?callable $unTransformer = null
    ) {
        $this->key = $key;
        $this->length = $length;
        $this->padCharacter = $padCharacter;
        $this->padPlacement = $padPlacement;
        $this->allowedCharacters = $allowedCharacters;
        $this->transformer = $transformer;
        $this->unTransformer = $unTransformer;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getPadCharacter(): string
    {
        return $this->padCharacter ?? ' ';
    }

    public function getPadPlacement(): PadPlacement
    {
        return $this->padPlacement ?? PadPlacement::RIGHT();
    }

    public function getAllowedCharacters(): ?string
    {
        return $this->allowedCharacters;
    }

    public function transform(string $value)
    {
        if ($this->transformer === null) {
            return $value;
        }

        $transformer = $this->transformer;

        return $transformer($value);
    }

    public function unTransform($value): string
    {
        if ($this->unTransformer === null) {
            return $value;
        }

        $unTransformer = $this->unTransformer;

        return $unTransformer($value);
    }
}
