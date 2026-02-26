<?php

namespace Zhalil\Mapper\Validation;

use ReflectionClass;
use ReflectionProperty;
use Zhalil\Mapper\ErrorBag;
use Zhalil\Mapper\Exception\ValidationException;
use Zhalil\Mapper\Validation\Rule\RuleInterface;

final class Validator
{
    private array $data;

    public function validate(object $object, string $className): void
    {
        $reflectionClass = new ReflectionClass($object);
        $this->data = $this->extractData($object, $reflectionClass);
        $errorBag = new ErrorBag();

        foreach ($reflectionClass->getProperties() as $property) {
            $this->validateProperty($object, $property, $errorBag);
        }

        if ($errorBag->count() > 0) {
            throw new ValidationException($errorBag, $className);
        }
    }

    private function extractData(object $object, ReflectionClass $class): array
    {
        $data = [];
        foreach ($class->getProperties() as $property) {
            if ($property->isInitialized($object)) {
                $data[$property->getName()] = $property->getValue($object);
            }
        }
        return $data;
    }

    private function validateProperty(object $object, ReflectionProperty $property, ErrorBag $errorBag): void
    {
        $propertyName = $property->getName();
        $value = $this->data[$propertyName] ?? null;

        $attributes = $property->getAttributes(ValidationAttributeInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

        foreach ($attributes as $attribute) {
            $validationAttribute = $attribute->newInstance();
            $rule = $validationAttribute->getRule();

            if (!$rule->validate($value, $propertyName, $this->data)) {
                $errorBag->add($propertyName, $this->formatMessage($rule, $propertyName, $value));
            }
        }
    }

    private function formatMessage(RuleInterface $rule, string $property, mixed $value): string
    {
        $message = $rule->message();
        return str_replace(':attribute', $property, $message);
    }
}
