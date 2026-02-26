<?php

namespace Zhalil\Mapper;

use Zhalil\Mapper\Attribute\ArrayOf;
use Zhalil\Mapper\Exception\MappingException;
use Zhalil\Mapper\Exception\ValidationException;
use Zhalil\Mapper\Validation\Validator;

final class Mapper
{
    private array $constructorDocCache = [];
    private array $useStatementsCache = [];

    public function __construct(private readonly Validator $validator) {}

    public function map(mixed $data): PendingMapping
    {
        return new PendingMapping($data, $this);
    }

    public function resolve(string $class, mixed $data, bool $isCollection = false): array|object
    {
        if ($isCollection) {
            if (!is_array($data)) {
                throw MappingException::invalidDataType('array', $data);
            }
            return array_map(fn ($item) => $this->resolveSingle($class, $item), $data);
        }
        
        if (!is_array($data)) {
            throw MappingException::invalidDataType('array', $data);
        }
        
        return $this->resolveSingle($class, $data);
    }

    private function resolveSingle(string $class, array $data): object
    {
        $reflectionClass = new \ReflectionClass($class);
        $object = $this->createObject($reflectionClass, $class, $data);
        $this->validate($object, $class);

        return $object;
    }

    private function createObject(\ReflectionClass $reflectionClass, string $className, array $data): object
    {
        $classStrategy = $this->resolveClassStrategy($reflectionClass);
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $isStrictClass = $reflectionClass->getAttributes(Attribute\Strict::class) !== [];
        $missingValues = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $inputKey = $this->resolveInputKey($property, $classStrategy, $propertyName);

            if (array_key_exists($inputKey, $data)) {
                $value = $this->resolvePropertyValue($property, $data[$inputKey], $reflectionClass);
                $property->setValue($object, $value);
            } elseif ($property->hasDefaultValue()) {
                continue;
            } elseif ($property->getType()?->allowsNull()) {
                $property->setValue($object, null);
            } else {
                $isStrictProperty = $isStrictClass || $property->getAttributes(Attribute\Strict::class) !== [];
                if ($isStrictProperty) {
                    $missingValues[] = $inputKey;
                }
            }
        }

        if ($missingValues !== []) {
            throw MappingException::missingRequiredFields($missingValues, $className, $data);
        }

        return $object;
    }

    private function resolvePropertyValue(
        \ReflectionProperty $property,
        mixed $value,
        \ReflectionClass $declaringClass
    ): mixed {
        if ($value === null) {
            return null;
        }

        $value = $this->castValue($property, $value);

        $arrayType = $this->resolveArrayType($property, $declaringClass);
        if ($arrayType !== null && is_array($value)) {
            return array_map(fn ($item) => $this->mapToObject($item, $arrayType), $value);
        }

        $singleType = $this->resolveSingleType($property, $declaringClass);
        if ($singleType !== null && is_array($value)) {
            return $this->mapToObject($value, $singleType);
        }

        return $value;
    }

    private function mapToObject(mixed $data, string $className): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        if (!class_exists($className)) {
            return $data;
        }

        return $this->resolveSingle($className, $data);
    }

    private function resolveArrayType(\ReflectionProperty $property, \ReflectionClass $declaringClass): ?string
    {
        $arrayOfAttributes = $property->getAttributes(ArrayOf::class);
        if (!empty($arrayOfAttributes)) {
            return $arrayOfAttributes[0]->newInstance()->className;
        }

        return $this->parsePhpDocArrayType($property, $declaringClass);
    }

    private function parsePhpDocArrayType(\ReflectionProperty $property, \ReflectionClass $declaringClass): ?string
    {
        $docComment = $property->getDocComment();
        $propertyName = $property->getName();

        if ($docComment !== false) {
            if (preg_match('/@var\s+([^\s\[\]]+)\[\]/', $docComment, $matches)) {
                return $this->resolveClassName($matches[1], $declaringClass);
            }
        }

        $constructorDoc = $this->getConstructorDocComment($declaringClass);
        if ($constructorDoc !== null) {
            $pattern = '/@param\s+([^\s\[\]]+)\[\]\s+\$' . preg_quote($propertyName, '/') . '/';
            if (preg_match($pattern, $constructorDoc, $matches)) {
                return $this->resolveClassName($matches[1], $declaringClass);
            }
        }

        return null;
    }

    private function resolveSingleType(\ReflectionProperty $property, \ReflectionClass $declaringClass): ?string
    {
        $type = $property->getType();
        if ($type === null) {
            return $this->parsePhpDocSingleType($property, $declaringClass);
        }

        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
            return $type->getName();
        }

        return null;
    }

    private function parsePhpDocSingleType(\ReflectionProperty $property, \ReflectionClass $declaringClass): ?string
    {
        $docComment = $property->getDocComment();
        $propertyName = $property->getName();

        if ($docComment !== false) {
            if (preg_match('/@var\s+([A-Z][a-zA-Z0-9\\\\_]*)\s/', $docComment, $matches)) {
                return $this->resolveClassName($matches[1], $declaringClass);
            }
        }

        $constructorDoc = $this->getConstructorDocComment($declaringClass);
        if ($constructorDoc !== null) {
            $pattern = '/@param\s+([A-Z][a-zA-Z0-9\\\\_]*)\s+\$' . preg_quote($propertyName, '/') . '/';
            if (preg_match($pattern, $constructorDoc, $matches)) {
                return $this->resolveClassName($matches[1], $declaringClass);
            }
        }

        return null;
    }

    private function getConstructorDocComment(\ReflectionClass $class): ?string
    {
        $className = $class->getName();
        if (!isset($this->constructorDocCache[$className])) {
            $constructor = $class->getConstructor();
            $this->constructorDocCache[$className] = $constructor?->getDocComment() ?: null;
        }
        return $this->constructorDocCache[$className];
    }

    private function resolveClassName(string $typeName, \ReflectionClass $contextClass): string
    {
        if (class_exists($typeName) || interface_exists($typeName)) {
            return $typeName;
        }

        if (str_contains($typeName, '\\')) {
            if (class_exists($typeName)) {
                return $typeName;
            }
        }

        $useStatements = $this->getUseStatements($contextClass);
        if (isset($useStatements[$typeName])) {
            return $useStatements[$typeName];
        }

        foreach ($useStatements as $alias => $fqcn) {
            $shortName = basename(str_replace('\\', '/', $fqcn));
            if ($shortName === $typeName && class_exists($fqcn)) {
                return $fqcn;
            }
        }

        $namespace = $contextClass->getNamespaceName();
        if ($namespace) {
            $fullyQualified = $namespace . '\\' . $typeName;
            if (class_exists($fullyQualified)) {
                return $fullyQualified;
            }
        }

        return $typeName;
    }

    private function getUseStatements(\ReflectionClass $class): array
    {
        $className = $class->getName();
        if (isset($this->useStatementsCache[$className])) {
            return $this->useStatementsCache[$className];
        }

        $fileName = $class->getFileName();
        if ($fileName === false) {
            $this->useStatementsCache[$className] = [];
            return [];
        }

        $content = file_get_contents($fileName);
        if ($content === false) {
            $this->useStatementsCache[$className] = [];
            return [];
        }

        $useStatements = [];
        if (preg_match_all('/^\s*use\s+([^;]+);/m', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $useClause = $match[1];
                $useClause = preg_replace('/\/\*.*?\*\//s', '', $useClause);
                
                if (preg_match('/^(.+?)\s+as\s+(\w+)$/i', trim($useClause), $aliasMatch)) {
                    $fqcn = trim($aliasMatch[1], '\\ ');
                    $alias = trim($aliasMatch[2]);
                    $useStatements[$alias] = $fqcn;
                } else {
                    $fqcn = trim($useClause, '\\ ');
                    $shortName = basename(str_replace('\\', '/', $fqcn));
                    $useStatements[$shortName] = $fqcn;
                }
            }
        }

        $this->useStatementsCache[$className] = $useStatements;
        return $useStatements;
    }

    private function resolveInputKey(
        \ReflectionProperty $property,
        Contract\NamingStrategyInterface $classStrategy,
        string $propertyName
    ): string {
        $mapFromAttributes = $property->getAttributes(Attribute\MapFrom::class);
        if (!empty($mapFromAttributes)) {
            return $mapFromAttributes[0]->newInstance()->name;
        }

        $propertyStrategy = $this->resolvePropertyStrategy($property, $classStrategy);
        return $propertyStrategy->convert($propertyName);
    }

    private function castValue(\ReflectionProperty $property, mixed $value): mixed
    {
        $castWithAttributes = $property->getAttributes(Attribute\CastWith::class);
        if (!empty($castWithAttributes)) {
            $casterClass = $castWithAttributes[0]->newInstance()->casterClass;
            try {
                $caster = new $casterClass();
                return $caster->cast($value);
            } catch (\Throwable $e) {
                throw MappingException::casterFailed($casterClass, $property->getName(), $e->getMessage());
            }
        }

        return $value;
    }

    private function resolveClassStrategy(\ReflectionClass $reflectionClass): Contract\NamingStrategyInterface
    {
        $attributes = $reflectionClass->getAttributes(Attribute\MapInputName::class);
        if (!empty($attributes)) {
            $attribute = $attributes[0]->newInstance();
            return new ($attribute->strategy)();
        }
        return new NamingStrategy\CamelCaseNamingStrategy();
    }

    private function resolvePropertyStrategy(
        \ReflectionProperty $property,
        Contract\NamingStrategyInterface $defaultStrategy
    ): Contract\NamingStrategyInterface {
        $attributes = $property->getAttributes(Attribute\MapInputName::class);
        if (!empty($attributes)) {
            $attribute = $attributes[0]->newInstance();
            return new ($attribute->strategy)();
        }
        return $defaultStrategy;
    }

    private function validate(object $object, string $className): void
    {
        $this->validator->validate($object, $className);
    }

    public function toArray(object $object): array
    {
        $reflectionClass = new \ReflectionClass($object);
        $result = [];
        $classStrategy = $this->resolveOutputClassStrategy($reflectionClass);

        foreach ($reflectionClass->getProperties() as $property) {
            $hiddenAttributes = $property->getAttributes(Attribute\Hidden::class);
            if (!empty($hiddenAttributes)) {
                continue;
            }

            if (!$property->isInitialized($object)) {
                continue;
            }

            $propertyName = $property->getName();
            $outputKey = $this->resolveOutputKey($property, $classStrategy, $propertyName);
            $value = $property->getValue($object);

            $result[$outputKey] = $this->serializeValue($value);
        }

        return $result;
    }

    private function resolveOutputClassStrategy(\ReflectionClass $reflectionClass): Contract\NamingStrategyInterface
    {
        $attributes = $reflectionClass->getAttributes(Attribute\MapOutputName::class);
        if (!empty($attributes)) {
            $attribute = $attributes[0]->newInstance();
            return new ($attribute->strategy)();
        }
        return new NamingStrategy\CamelCaseNamingStrategy();
    }

    private function resolveOutputKey(
        \ReflectionProperty $property,
        Contract\NamingStrategyInterface $defaultStrategy,
        string $propertyName
    ): string {
        $mapToAttributes = $property->getAttributes(Attribute\MapTo::class);
        if (!empty($mapToAttributes)) {
            return $mapToAttributes[0]->newInstance()->name;
        }

        $propertyStrategy = $this->resolveOutputPropertyStrategy($property, $defaultStrategy);
        return $propertyStrategy->convert($propertyName);
    }

    private function resolveOutputPropertyStrategy(
        \ReflectionProperty $property,
        Contract\NamingStrategyInterface $defaultStrategy
    ): Contract\NamingStrategyInterface {
        $attributes = $property->getAttributes(Attribute\MapOutputName::class);
        if (!empty($attributes)) {
            $attribute = $attributes[0]->newInstance();
            return new ($attribute->strategy)();
        }
        return $defaultStrategy;
    }

    private function serializeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map($this->serializeValue(...), $value);
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::ATOM);
        }

        if (is_object($value)) {
            return $this->toArray($value);
        }

        return $value;
    }

    public function toJson(object $object): string
    {
        return json_encode($this->toArray($object), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
