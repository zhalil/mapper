<?php

namespace Zhalil\Mapper\Validation\Rule;

final class StartsWith implements RuleInterface
{
    private readonly array $prefixes;

    public function __construct(string|array ...$prefixes)
    {
        $this->prefixes = count($prefixes) === 1 && is_array($prefixes[0])
            ? $prefixes[0]
            : $prefixes;
    }

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        foreach ($this->prefixes as $prefix) {
            if (str_starts_with($value, $prefix)) {
                return true;
            }
        }
        return false;
    }

    public function message(): string
    {
        $prefixes = implode(', ', $this->prefixes);
        return "The :attribute must start with one of: {$prefixes}.";
    }
}
