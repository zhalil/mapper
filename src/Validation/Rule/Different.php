<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Different implements RuleInterface
{
    public function __construct(private readonly string $otherField) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        return !isset($data[$this->otherField]) || $value !== $data[$this->otherField];
    }

    public function message(): string
    {
        return "The :attribute and {$this->otherField} must be different.";
    }
}
