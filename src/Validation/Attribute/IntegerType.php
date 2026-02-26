<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\IntegerType as IntegerTypeRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class IntegerType implements ValidationAttributeInterface
{
    public function getRule(): IntegerTypeRule
    {
        return new IntegerTypeRule();
    }
}
