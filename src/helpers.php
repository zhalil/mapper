<?php

namespace Zhalil\Mapper;

function map(mixed $data): PendingMapping
{
    return MapperFactory::create()->map($data);
}
