<?php

namespace Zhalil\Mapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MapFrom
{
    public function __construct(
        public readonly string $name
    ) {}
}
