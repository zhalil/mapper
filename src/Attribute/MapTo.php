<?php

namespace Zhalil\Mapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MapTo
{
    public function __construct(
        public readonly string $name
    ) {}
}
