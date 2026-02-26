<?php

namespace Zhalil\Mapper\Validation\Rule;

final class NotRegex implements RuleInterface
{
    public function __construct(private readonly string $pattern) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        return preg_match($this->pattern, $value) === 0;
    }

    public function message(): string
    {
        return 'The :attribute format is invalid.';
    }
}
