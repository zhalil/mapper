<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\EnumRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Enum implements ValidationAttributeInterface
{
    public function __construct(private readonly string $enumClass) {}

    public function getRule(): EnumRule
    {
        return new EnumRule($this->enumClass);
    }
}
