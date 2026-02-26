<?php

namespace Zhalil\Mapper\Validation\Rule;

final class DateFormat implements RuleInterface
{
    private readonly array $formats;

    public function __construct(string|array ...$formats)
    {
        $this->formats = count($formats) === 1 && is_array($formats[0])
            ? $formats[0]
            : $formats;
    }

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }
        if ($value instanceof \DateTimeInterface) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        foreach ($this->formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $value);
            if ($parsed !== false && $parsed->format($format) === $value) {
                return true;
            }
        }
        return false;
    }

    public function message(): string
    {
        $formats = implode(', ', $this->formats);
        return "The :attribute does not match the format: {$formats}.";
    }
}
