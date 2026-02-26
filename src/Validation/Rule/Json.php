<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Json implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid JSON string.';
    }
}
