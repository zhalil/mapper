<?php

namespace Zhalil\Mapper\Tests\NamingStrategy;

use PHPUnit\Framework\TestCase;
use Zhalil\Mapper\NamingStrategy\CamelCaseNamingStrategy;

class CamelCaseNamingStrategyTest extends TestCase
{
    public function testConvertReturnsSameString(): void
    {
        $strategy = new CamelCaseNamingStrategy();

        $this->assertSame('firstName', $strategy->convert('firstName'));
        $this->assertSame('emailAddress', $strategy->convert('emailAddress'));
        $this->assertSame('id', $strategy->convert('id'));
    }
}
