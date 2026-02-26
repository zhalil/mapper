<?php

namespace Zhalil\Mapper\Validation\Rule;

final class MultipleOf implements RuleInterface
{
    public function __construct(private readonly int|float $divisor) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_numeric($value) || $this->divisor == 0) {
            return false;
        }
        return $value % $this->divisor === 0;
    }

    public function message(): string
    {
        return "The :attribute must be a multiple of {$this->divisor}.";
    }
}
