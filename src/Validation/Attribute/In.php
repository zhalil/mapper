<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\In as InRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class In implements ValidationAttributeInterface
{
    private readonly array $values;

    public function __construct(mixed ...$values)
    {
        $this->values = count($values) === 1 && is_array($values[0])
            ? $values[0]
            : $values;
    }

    public function getRule(): InRule
    {
        return new InRule(...$this->values);
    }
}
