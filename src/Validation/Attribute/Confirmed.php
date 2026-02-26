<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Confirmed as ConfirmedRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Confirmed implements ValidationAttributeInterface
{
    public function getRule(): ConfirmedRule
    {
        return new ConfirmedRule();
    }
}
