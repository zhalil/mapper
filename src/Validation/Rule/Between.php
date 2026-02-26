<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Between implements RuleInterface
{
    public function __construct(
        private readonly int|float $min,
        private readonly int|float $max
    ) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_numeric($value)) {
            return $value >= $this->min && $value <= $this->max;
        }

        if (is_string($value)) {
            $length = mb_strlen($value);
            return $length >= $this->min && $length <= $this->max;
        }

        if (is_array($value)) {
            $count = count($value);
            return $count >= $this->min && $count <= $this->max;
        }

        return false;
    }

    public function message(): string
    {
        return "The :attribute must be between {$this->min} and {$this->max}.";
    }
}
