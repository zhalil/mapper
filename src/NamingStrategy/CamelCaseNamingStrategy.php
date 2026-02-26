<?php

namespace Zhalil\Mapper\NamingStrategy;

use Zhalil\Mapper\Contract\NamingStrategyInterface;

final class CamelCaseNamingStrategy implements NamingStrategyInterface
{
    public function convert(string $propertyName): string
    {
        return $propertyName;
    }
}
