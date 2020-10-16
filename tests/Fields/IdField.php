<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests\Fields;

use Devvoh\FixedWidth\Field;
use Devvoh\FixedWidth\Field\SupportsPadding;
use Devvoh\FixedWidth\Field\SupportsTransforming;
use Devvoh\FixedWidth\PadPlacement;

class IdField implements Field, SupportsPadding, SupportsTransforming
{
    public function getKey(): string
    {
        return 'id';
    }

    public function getLength(): int
    {
        return 10;
    }

    public function getPadCharacter(): string
    {
        return '0';
    }

    public function getPadPlacement(): PadPlacement
    {
        return PadPlacement::LEFT();
    }

    public function transform(string $value)
    {
        return (int)$value;
    }

    public function unTransform($value): string
    {
        return (string)$value;
    }
}
