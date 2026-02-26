<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Required as RequiredRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Required implements ValidationAttributeInterface
{
    public function getRule(): RequiredRule
    {
        return new RequiredRule();
    }
}
