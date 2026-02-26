<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Ulid implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        return preg_match('/^[0-7][0-9a-hjkmnp-tv-zA-HJKMNP-TV-Z]{25}$/i', $value) === 1;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid ULID.';
    }
}
