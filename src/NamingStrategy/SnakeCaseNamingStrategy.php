<?php

namespace Zhalil\Mapper\NamingStrategy;

use Zhalil\Mapper\Contract\NamingStrategyInterface;

final class SnakeCaseNamingStrategy implements NamingStrategyInterface
{
    public function convert(string $propertyName): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $propertyName));
    }
}
