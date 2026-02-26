<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Numeric implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        return is_numeric($value);
    }

    public function message(): string
    {
        return 'The :attribute must be a number.';
    }
}
