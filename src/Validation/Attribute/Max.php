<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Max as MaxRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Max implements ValidationAttributeInterface
{
    public function __construct(private readonly int|float $max) {}

    public function getRule(): MaxRule
    {
        return new MaxRule($this->max);
    }
}
