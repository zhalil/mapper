<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Size as SizeRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Size implements ValidationAttributeInterface
{
    public function __construct(private readonly int|float $size) {}

    public function getRule(): SizeRule
    {
        return new SizeRule($this->size);
    }
}
