<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Date implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if ($value instanceof \DateTimeInterface) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        return strtotime($value) !== false;
    }

    public function message(): string
    {
        return 'The :attribute is not a valid date.';
    }
}
