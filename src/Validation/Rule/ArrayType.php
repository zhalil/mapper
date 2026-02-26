<?php

namespace Zhalil\Mapper\Validation\Rule;

final class ArrayType implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        return is_array($value);
    }

    public function message(): string
    {
        return 'The :attribute must be an array.';
    }
}
