<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Field;

use Devvoh\FixedWidth\PadPlacement;

interface SupportsPadding
{
    public function getPadCharacter(): string;
    public function getPadPlacement(): PadPlacement;
}
