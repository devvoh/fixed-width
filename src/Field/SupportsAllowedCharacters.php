<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Field;

interface SupportsAllowedCharacters
{
    public function getAllowedCharacters(): ?string;
}
