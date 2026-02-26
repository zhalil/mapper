<?php

namespace Zhalil\Mapper\Contract;

interface CasterInterface
{
    public function cast(mixed $input): mixed;
}
