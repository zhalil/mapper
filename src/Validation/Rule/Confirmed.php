<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Confirmed implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        $confirmationField = $property . '_confirmation';
        return isset($data[$confirmationField]) && $value === $data[$confirmationField];
    }

    public function message(): string
    {
        return 'The :attribute confirmation does not match.';
    }
}
