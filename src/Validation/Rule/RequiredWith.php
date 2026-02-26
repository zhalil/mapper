<?php

namespace Zhalil\Mapper\Validation\Rule;

final class RequiredWith implements RuleInterface
{
    private readonly array $fields;

    public function __construct(string|array ...$fields)
    {
        $this->fields = count($fields) === 1 && is_array($fields[0])
            ? $fields[0]
            : $fields;
    }

    public function validate(mixed $value, string $property, array $data): bool
    {
        foreach ($this->fields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                return $value !== null && $value !== '';
            }
        }
        
        return true;
    }

    public function message(): string
    {
        $fields = implode(', ', $this->fields);
        return "The :attribute field is required when any of {$fields} is present.";
    }
}
