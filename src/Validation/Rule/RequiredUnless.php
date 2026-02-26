<?php

namespace Zhalil\Mapper\Validation\Rule;

final class RequiredUnless implements RuleInterface
{
    private readonly array $values;

    public function __construct(private readonly string $otherField, mixed ...$values)
    {
        $this->values = count($values) === 1 && is_array($values[0])
            ? $values[0]
            : $values;
    }

    public function validate(mixed $value, string $property, array $data): bool
    {
        if (!isset($data[$this->otherField])) {
            return $value !== null && $value !== '';
        }
        
        $otherValue = $data[$this->otherField];
        
        if (!in_array($otherValue, $this->values, true)) {
            return $value !== null && $value !== '';
        }
        
        return true;
    }

    public function message(): string
    {
        $values = implode(', ', array_map('json_encode', $this->values));
        return "The :attribute field is required unless {$this->otherField} is in {$values}.";
    }
}
