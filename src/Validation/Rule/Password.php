<?php

namespace Zhalil\Mapper\Validation\Rule;

final class Password implements RuleInterface
{
    public function __construct(
        private readonly int $min = 8,
        private readonly bool $letters = false,
        private readonly bool $mixedCase = false,
        private readonly bool $numbers = false,
        private readonly bool $symbols = false,
        private readonly bool $uncompromised = false,
        private readonly int $uncompromisedThreshold = 0
    ) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        if (mb_strlen($value) < $this->min) {
            return false;
        }

        if ($this->letters && !preg_match('/[\pL\pM]/u', $value)) {
            return false;
        }

        if ($this->mixedCase) {
            if (!preg_match('/\p{Ll}/u', $value) || !preg_match('/\p{Lu}/u', $value)) {
                return false;
            }
        }

        if ($this->numbers && !preg_match('/\pN/u', $value)) {
            return false;
        }

        if ($this->symbols && !preg_match('/[\pP\pS]/u', $value)) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        $requirements = ["at least {$this->min} characters"];

        if ($this->letters) {
            $requirements[] = 'at least one letter';
        }
        if ($this->mixedCase) {
            $requirements[] = 'both uppercase and lowercase letters';
        }
        if ($this->numbers) {
            $requirements[] = 'at least one number';
        }
        if ($this->symbols) {
            $requirements[] = 'at least one symbol';
        }

        return 'The :attribute must contain ' . implode(', ', $requirements) . '.';
    }
}
