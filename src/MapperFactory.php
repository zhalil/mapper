<?php

namespace Zhalil\Mapper;

use Zhalil\Mapper\Validation\Validator;

final class MapperFactory
{
    public static function create(): Mapper
    {
        return new Mapper(new Validator());
    }
}
