<?php

namespace Zhalil\Mapper\Attribute;

use Attribute;
use Zhalil\Mapper\NamingStrategy\CamelCaseNamingStrategy;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
final class MapInputName
{
    public function __construct(
        public readonly string $strategy = CamelCaseNamingStrategy::class
    ) {}
}
