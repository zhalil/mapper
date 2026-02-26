<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Numeric as NumericRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Numeric implements ValidationAttributeInterface
{
    public function getRule(): NumericRule
    {
        return new NumericRule();
    }
}
