<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Lowercase implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        return mb_strtolower($value) === $value;
    }

    public function message(): string
    {
        return 'The :attribute must be lowercase.';
    }
}
