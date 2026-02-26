<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Prohibited implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        return $value === null;
    }

    public function message(): string
    {
        return 'The :attribute field is prohibited.';
    }
}
