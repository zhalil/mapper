<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Nullable implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        return true;
    }

    public function message(): string
    {
        return '';
    }
}
