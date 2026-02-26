<?php

namespace Zhalil\Mapper;

use Zhalil\Mapper\Exception\MappingException;
use Zhalil\Mapper\Exception\ValidationException;

final class PendingMapping
{
    private bool $isCollection = false;

    public function __construct(
        private readonly mixed $data,
        private readonly Mapper $mapper
    ) {}

    /**
     * Map data to the specified class.
     *
     * @template T of object
     * @param class-string<T> $class The target class name
     * @return ($isCollection is true ? array<int, T> : T)
     *
     * @throws MappingException When data cannot be mapped to the target class
     *                         (missing required fields, invalid data types, caster failures)
     * @throws ValidationException When validation rules defined via attributes fail.
     *                            Use $e->errors() to get ErrorBag with methods:
     *                            - toArray(): array - get errors as associative array
     *                            - toJson(): string - get errors as JSON string
     *                            - has(string $property): bool - check if property has errors
     *                            - get(string $property): array - get errors for specific property
     */
    public function to(string $class): array|object
    {
        return $this->mapper->resolve($class, $this->data, $this->isCollection);
    }

    public function collection(): self
    {
        $this->isCollection = true;
        return $this;
    }

    public function toArray(): array
    {
        if ($this->isCollection && is_array($this->data)) {
            return array_map(
                fn ($item) => is_object($item) ? $this->mapper->toArray($item) : $item,
                $this->data
            );
        }

        if (is_object($this->data)) {
            return $this->mapper->toArray($this->data);
        }

        return $this->data;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
