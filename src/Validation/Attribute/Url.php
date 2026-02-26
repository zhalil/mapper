<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Url as UrlRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Url implements ValidationAttributeInterface
{
    private ?array $protocols;

    public function __construct(?array $protocols = null)
    {
        $this->protocols = $protocols;
    }

    public function getRule(): UrlRule
    {
        return new UrlRule($this->protocols);
    }
}
