<?php

namespace Zhalil\Mapper\Validation\Rule;

final class DoesntEndWith implements RuleInterface
{
    private readonly array $suffixes;

    public function __construct(string|array ...$suffixes)
    {
        $this->suffixes = count($suffixes) === 1 && is_array($suffixes[0])
            ? $suffixes[0]
            : $suffixes;
    }

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        foreach ($this->suffixes as $suffix) {
            if (str_ends_with($value, $suffix)) {
                return false;
            }
        }
        return true;
    }

    public function message(): string
    {
        $suffixes = implode(', ', $this->suffixes);
        return "The :attribute must not end with any of: {$suffixes}.";
    }
}
