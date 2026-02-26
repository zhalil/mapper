<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Timezone implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        return in_array($value, \DateTimeZone::listIdentifiers(), true);
    }

    public function message(): string
    {
        return 'The :attribute must be a valid timezone.';
    }
}
