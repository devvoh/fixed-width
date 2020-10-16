<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests\Fields;

use Devvoh\FixedWidth\Field;
use Devvoh\FixedWidth\Field\SupportsTransforming;
use Devvoh\FixedWidth\PadPlacement;

class BooleanField implements Field, SupportsTransforming
{
    public function getKey(): string
    {
        return 'boolean';
    }

    public function getLength(): int
    {
        return 1;
    }

    public function transform(string $value)
    {
        switch ($value) {
            case '0':
                return false;

            case '1':
                return true;
        }
    }

    public function unTransform($value): string
    {
        return $value ? '1' : '0';
    }
}
