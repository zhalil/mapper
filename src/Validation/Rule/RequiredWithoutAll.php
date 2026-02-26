<?php

namespace Zhalil\Mapper\Validation\Rule;

final class RequiredWithoutAll implements RuleInterface
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
                return true;
            }
        }
        
        return $value !== null && $value !== '';
    }

    public function message(): string
    {
        $fields = implode(', ', $this->fields);
        return "The :attribute field is required when all of {$fields} are missing.";
    }
}
