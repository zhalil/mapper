<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\ArrayType as ArrayTypeRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ArrayType implements ValidationAttributeInterface
{
    public function getRule(): ArrayTypeRule
    {
        return new ArrayTypeRule();
    }
}
