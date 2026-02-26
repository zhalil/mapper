<?php

namespace Zhalil\Mapper\Validation\Rule;

final class GreaterThanOrEqualTo implements RuleInterface
{
    public function __construct(private readonly string $otherField) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!isset($data[$this->otherField])) {
            return false;
        }
        return $value >= $data[$this->otherField];
    }

    public function message(): string
    {
        return "The :attribute must be greater than or equal to {$this->otherField}.";
    }
}
