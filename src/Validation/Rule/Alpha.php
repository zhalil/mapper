<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Alpha implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        return preg_match('/^[\pL\pM]+$/u', $value) === 1;
    }

    public function message(): string
    {
        return 'The :attribute may only contain letters.';
    }
}
