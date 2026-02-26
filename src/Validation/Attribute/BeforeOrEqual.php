<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\BeforeOrEqual as BeforeOrEqualRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class BeforeOrEqual implements ValidationAttributeInterface
{
    public function __construct(private readonly string $date) {}

    public function getRule(): BeforeOrEqualRule
    {
        return new BeforeOrEqualRule($this->date);
    }
}
