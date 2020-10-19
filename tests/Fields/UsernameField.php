<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests\Fields;

use Devvoh\FixedWidth\Field;
use Devvoh\FixedWidth\Field\FieldWithPadding;
use Devvoh\FixedWidth\PadPlacement;

class UsernameField implements Field, FieldWithPadding
{
    public function getKey(): string
    {
        return 'username';
    }

    public function getLength(): int
    {
        return 20;
    }

    public function getPadCharacter(): string
    {
        return ' ';
    }

    public function getPadPlacement(): PadPlacement
    {
        return PadPlacement::RIGHT();
    }
}
