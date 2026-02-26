<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Url implements RuleInterface
{
    private ?array $protocols;

    public function __construct(?array $protocols = null)
    {
        $this->protocols = $protocols;
    }

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }

        if ($this->protocols !== null) {
            $protocol = parse_url($value, PHP_URL_SCHEME);
            if ($protocol === null || !in_array($protocol, $this->protocols, true)) {
                return false;
            }
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid URL.';
    }
}
