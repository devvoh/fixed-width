<?php declare(strict_types=1);

namespace Devvoh\FixedWidth;

use Devvoh\FixedWidth\PadPlacement;

interface Field
{
    public function getKey(): string;
    public function getLength(): int;
}
