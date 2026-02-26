<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\AfterOrEqual as AfterOrEqualRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class AfterOrEqual implements ValidationAttributeInterface
{
    public function __construct(private readonly string $date) {}

    public function getRule(): AfterOrEqualRule
    {
        return new AfterOrEqualRule($this->date);
    }
}
