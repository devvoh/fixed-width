<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests\Fields;

use Devvoh\FixedWidth\Field;
use Devvoh\FixedWidth\Field\SupportsAllowedCharacters;

class RestrictedField implements Field, SupportsAllowedCharacters
{
    public function getKey(): string
    {
        return 'restricted';
    }

    public function getLength(): int
    {
        return 1;
    }

    public function getAllowedCharacters(): ?string
    {
        return 'abc';
    }
}
