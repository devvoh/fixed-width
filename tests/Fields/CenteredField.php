<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Tests\Fields;

use Devvoh\FixedWidth\Field;
use Devvoh\FixedWidth\Field\SupportsPadding;
use Devvoh\FixedWidth\PadPlacement;

class CenteredField implements Field, SupportsPadding
{
    public function getKey(): string
    {
        return 'centered';
    }

    public function getLength(): int
    {
        return 10;
    }

    public function getPadCharacter(): string
    {
        return '.';
    }

    public function getPadPlacement(): PadPlacement
    {
        return PadPlacement::BOTH();
    }
}
