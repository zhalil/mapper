<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Min implements RuleInterface
{
    public function __construct(private readonly int|float $min) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_numeric($value)) {
            return $value >= $this->min;
        }

        if (is_string($value)) {
            return mb_strlen($value) >= $this->min;
        }

        if (is_array($value)) {
            return count($value) >= $this->min;
        }

        return false;
    }

    public function message(): string
    {
        return "The :attribute must be at least {$this->min}.";
    }
}
