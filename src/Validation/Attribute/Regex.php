<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Regex as RegexRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Regex implements ValidationAttributeInterface
{
    public function __construct(private readonly string $pattern) {}

    public function getRule(): RegexRule
    {
        return new RegexRule($this->pattern);
    }
}
