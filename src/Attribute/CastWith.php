<?php

namespace Zhalil\Mapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class CastWith
{
    public function __construct(
        public readonly string $casterClass
    ) {}
}
