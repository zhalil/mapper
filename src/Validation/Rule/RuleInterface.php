<?php

namespace Zhalil\Mapper\Validation\Rule;

interface RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool;

    public function message(): string;
}
