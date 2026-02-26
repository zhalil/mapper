<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Required implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        if (is_array($value) && count($value) === 0) {
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return 'The :attribute field is required.';
    }
}
