<?php

namespace Zhalil\Mapper\Validation\Rule;

final class NotIn implements RuleInterface
{
    private readonly array $values;

    public function __construct(mixed ...$values)
    {
        $this->values = count($values) === 1 && is_array($values[0])
            ? $values[0]
            : $values;
    }

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        return !in_array($value, $this->values, true);
    }

    public function message(): string
    {
        $values = implode(', ', array_map('json_encode', $this->values));
        return "The :attribute must not be one of: {$values}.";
    }
}
