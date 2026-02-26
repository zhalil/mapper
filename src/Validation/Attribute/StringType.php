<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\StringType as StringTypeRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class StringType implements ValidationAttributeInterface
{
    public function getRule(): StringTypeRule
    {
        return new StringTypeRule();
    }
}
