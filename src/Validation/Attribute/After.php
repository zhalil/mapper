<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\After as AfterRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class After implements ValidationAttributeInterface
{
    public function __construct(private readonly string $date) {}

    public function getRule(): AfterRule
    {
        return new AfterRule($this->date);
    }
}
