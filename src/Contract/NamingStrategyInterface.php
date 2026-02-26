<?php

namespace Zhalil\Mapper\Contract;

interface NamingStrategyInterface
{
    public function convert(string $propertyName): string;
}
