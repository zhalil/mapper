<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\ProhibitedUnless as ProhibitedUnlessRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ProhibitedUnless implements ValidationAttributeInterface
{
    private readonly array $values;

    public function __construct(private readonly string $otherField, mixed ...$values)
    {
        $this->values = count($values) === 1 && is_array($values[0])
            ? $values[0]
            : $values;
    }

    public function getRule(): ProhibitedUnlessRule
    {
        return new ProhibitedUnlessRule($this->otherField, ...$this->values);
    }
}
