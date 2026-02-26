<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Date as DateRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Date implements ValidationAttributeInterface
{
    public function getRule(): DateRule
    {
        return new DateRule();
    }
}
