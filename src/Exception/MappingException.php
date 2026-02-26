<?php

namespace Zhalil\Mapper\Exception;

use RuntimeException;

final class MappingException extends RuntimeException
{
    private ?string $className = null;
    private array $missingFields = [];
    private array $providedData = [];

    public static function missingRequiredField(string $field): self
    {
        $exception = new self(sprintf("Missing required field: '%s'", $field));
        $exception->missingFields = [$field];
        return $exception;
    }

    public static function missingRequiredFields(array $fields, string $className, array $providedData = []): self
    {
        $fieldList = implode("', '", $fields);
        $exception = new self(sprintf(
            "Failed to map data to %s.\n\nMissing required fields: ['%s']",
            $className,
            $fieldList
        ));
        $exception->className = $className;
        $exception->missingFields = $fields;
        $exception->providedData = $providedData;
        return $exception;
    }

    public static function invalidDataType(string $expected, mixed $actual): self
    {
        $actualType = is_object($actual) ? get_class($actual) : gettype($actual);
        return new self(sprintf(
            "Invalid data type: expected %s, got %s",
            $expected,
            $actualType
        ));
    }

    public static function casterFailed(string $casterClass, string $property, string $reason): self
    {
        return new self(sprintf(
            "Failed to cast property '%s' using %s: %s",
            $property,
            $casterClass,
            $reason
        ));
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function getMissingFields(): array
    {
        return $this->missingFields;
    }

    public function getProvidedData(): array
    {
        return $this->providedData;
    }
}
