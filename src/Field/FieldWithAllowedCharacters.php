<?php declare(strict_types=1);

namespace Devvoh\FixedWidth\Field;

interface FieldWithAllowedCharacters
{
    public function getAllowedCharacters(): ?string;
}
