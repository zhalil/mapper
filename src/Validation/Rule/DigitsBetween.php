<?php

namespace Zhalil\Mapper\Validation\Rule;

final class DigitsBetween implements RuleInterface
{
    public function __construct(
        private readonly int $min,
        private readonly int $max
    ) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_numeric($value)) {
            return false;
        }
        $digits = strlen((string) abs((int) $value));
        return $digits >= $this->min && $digits <= $this->max;
    }

    public function message(): string
    {
        return "The :attribute must be between {$this->min} and {$this->max} digits.";
    }
}
