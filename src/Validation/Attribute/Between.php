<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Between as BetweenRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Between implements ValidationAttributeInterface
{
    public function __construct(
        private readonly int|float $min,
        private readonly int|float $max
    ) {}

    public function getRule(): BetweenRule
    {
        return new BetweenRule($this->min, $this->max);
    }
}
