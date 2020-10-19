<?php declare(strict_types=1);

namespace Devvoh\FixedWidth;

use MyCLabs\Enum\Enum;

/**
 * @method static PadPlacement LEFT()
 * @method static PadPlacement RIGHT()
 * @method static PadPlacement BOTH()
 */
class PadPlacement extends Enum
{
    private const LEFT = STR_PAD_LEFT;
    private const RIGHT = STR_PAD_RIGHT;
    private const BOTH = STR_PAD_BOTH;
}
