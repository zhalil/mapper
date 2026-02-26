<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Uuid as UuidRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Uuid implements ValidationAttributeInterface
{
    public function getRule(): UuidRule
    {
        return new UuidRule();
    }
}
