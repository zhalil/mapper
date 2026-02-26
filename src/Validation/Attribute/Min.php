<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Min as MinRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Min implements ValidationAttributeInterface
{
    public function __construct(private readonly int|float $min) {}

    public function getRule(): MinRule
    {
        return new MinRule($this->min);
    }
}
