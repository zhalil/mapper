<?php

namespace Zhalil\Mapper\Validation\Rule;

final class StringType implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        return is_string($value);
    }

    public function message(): string
    {
        return 'The :attribute must be a string.';
    }
}
