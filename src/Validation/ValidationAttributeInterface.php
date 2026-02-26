<?php

namespace Zhalil\Mapper\Validation;

use Zhalil\Mapper\Validation\Rule\RuleInterface;

interface ValidationAttributeInterface
{
    public function getRule(): RuleInterface;
}
