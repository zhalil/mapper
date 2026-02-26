<?php

namespace Zhalil\Mapper\Validation\Rule;

final class AfterOrEqual implements RuleInterface
{
    public function __construct(private readonly string $date) {}

    public function validate(mixed $value, string $property, array $data): bool
    {
        if ($value === null) {
            return true;
        }

        $valueTimestamp = $this->getTimestamp($value);
        if ($valueTimestamp === null) {
            return false;
        }

        $compareTimestamp = $this->getCompareTimestamp($this->date);
        if ($compareTimestamp === null) {
            return false;
        }

        return $valueTimestamp >= $compareTimestamp;
    }

    private function getTimestamp(mixed $value): ?int
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->getTimestamp();
        }

        if (is_string($value)) {
            $timestamp = strtotime($value);
            return $timestamp !== false ? $timestamp : null;
        }

        return null;
    }

    private function getCompareTimestamp(string $date): ?int
    {
        if (in_array($date, ['today', 'tomorrow', 'yesterday'], true)) {
            return strtotime($date);
        }

        $timestamp = strtotime($date);
        return $timestamp !== false ? $timestamp : null;
    }

    public function message(): string
    {
        return "The :attribute must be a date after or equal to {$this->date}.";
    }
}
