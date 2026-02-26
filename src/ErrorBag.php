<?php

namespace Zhalil\Mapper;

final class ErrorBag implements \JsonSerializable
{
    /** @var array<string, string[]> */
    private array $errors = [];

    public function add(string $property, string $message): void
    {
        $this->errors[$property][] = $message;
    }

    public function has(string $property): bool
    {
        return isset($this->errors[$property]) && count($this->errors[$property]) > 0;
    }

    public function get(string $property): array
    {
        return $this->errors[$property] ?? [];
    }

    public function all(): array
    {
        return $this->errors;
    }

    public function toArray(): array
    {
        return $this->errors;
    }

    public function toJson(int $flags = JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR): string
    {
        return json_encode($this, $flags);
    }

    public function isEmpty(): bool
    {
        return empty($this->errors);
    }

    public function count(): int
    {
        return array_sum(array_map('count', $this->errors));
    }

    public function jsonSerialize(): array
    {
        return $this->errors;
    }
}
