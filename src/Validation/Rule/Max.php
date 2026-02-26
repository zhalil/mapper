<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Max implements RuleInterface
{
    public function __construct(private readonly int|float $max) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_numeric($value)) {
            return $value <= $this->max;
        }

        if (is_string($value)) {
            return mb_strlen($value) <= $this->max;
        }

        if (is_array($value)) {
            return count($value) <= $this->max;
        }

        return false;
    }

    public function message(): string
    {
        return "The :attribute may not be greater than {$this->max}.";
    }
}
