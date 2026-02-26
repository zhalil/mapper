<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Digits implements RuleInterface
{
    public function __construct(private readonly int $digits) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_numeric($value)) {
            return false;
        }
        return strlen((string) (int) $value) === $this->digits;
    }

    public function message(): string
    {
        return "The :attribute must be {$this->digits} digits.";
    }
}
