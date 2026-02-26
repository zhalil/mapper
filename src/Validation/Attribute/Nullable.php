<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Nullable as NullableRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Nullable implements ValidationAttributeInterface
{
    public function getRule(): NullableRule
    {
        return new NullableRule();
    }
}
