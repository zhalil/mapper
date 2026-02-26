<?php

namespace Zhalil\Mapper\Validation\Attribute;

use Attribute;
use Zhalil\Mapper\Validation\Rule\Password as PasswordRule;
use Zhalil\Mapper\Validation\ValidationAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Password implements ValidationAttributeInterface
{
    public function __construct(
        private readonly int $min = 8,
        private readonly bool $letters = false,
        private readonly bool $mixedCase = false,
        private readonly bool $numbers = false,
        private readonly bool $symbols = false
    ) {}

    public function getRule(): PasswordRule
    {
        return new PasswordRule(
            $this->min,
            $this->letters,
            $this->mixedCase,
            $this->numbers,
            $this->symbols
        );
    }
}
