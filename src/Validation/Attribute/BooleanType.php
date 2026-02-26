<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\BooleanType as BooleanTypeRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class BooleanType implements ValidationAttributeInterface
{
    public function getRule(): BooleanTypeRule
    {
        return new BooleanTypeRule();
    }
}
