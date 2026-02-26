<?php

namespace Zhalil\Mapper\Validation\Rule;

final class BooleanType implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        return is_bool($value);
    }

    public function message(): string
    {
        return 'The :attribute field must be true or false.';
    }
}
