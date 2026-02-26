<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Enum implements RuleInterface
{
    private readonly string $enumClass;

    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }

        if (!enum_exists($this->enumClass)) {
            return false;
        }

        if ($value instanceof \BackedEnum) {
            return $value instanceof $this->enumClass;
        }

        if ($value instanceof \UnitEnum) {
            return $value instanceof $this->enumClass;
        }

        foreach ($this->enumClass::cases() as $case) {
            if ($case instanceof \BackedEnum && $case->value === $value) {
                return true;
            }
            if ($case->name === $value) {
                return true;
            }
        }

        return false;
    }

    public function message(): string
    {
        return "The :attribute must be a valid {$this->enumClass} enum value.";
    }
}
