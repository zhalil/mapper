<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Email as EmailRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Email implements ValidationAttributeInterface
{
    public function getRule(): EmailRule
    {
        return new EmailRule();
    }
}
