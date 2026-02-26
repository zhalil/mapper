<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Ipv6 implements RuleInterface
{
    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid IPv6 address.';
    }
}
