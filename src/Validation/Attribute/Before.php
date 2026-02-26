<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Before as BeforeRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Before implements ValidationAttributeInterface
{
    public function __construct(private readonly string $date) {}

    public function getRule(): BeforeRule
    {
        return new BeforeRule($this->date);
    }
}
