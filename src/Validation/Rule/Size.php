<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Size implements RuleInterface
{
    public function __construct(private readonly int|float $size) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_numeric($value)) {
            return $value == $this->size;
        }

        if (is_string($value)) {
            return mb_strlen($value) === $this->size;
        }

        if (is_array($value)) {
            return count($value) === $this->size;
        }

        return false;
    }

    public function message(): string
    {
        return "The :attribute must be {$this->size}.";
    }
}
