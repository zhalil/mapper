<?php

namespace Zhalil\Mapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ArrayOf
{
    public function __construct(
        public readonly string $className
    ) {}
}
