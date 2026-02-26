<?php

namespace Zhalil\Mapper\Tests\NamingStrategy;

use PHPUnit\Framework\TestCase;
use Zhalil\Mapper\NamingStrategy\SnakeCaseNamingStrategy;

class SnakeCaseNamingStrategyTest extends TestCase
{
    public function testConvertCamelCaseToSnakeCase(): void
    {
        $strategy = new SnakeCaseNamingStrategy();

        $this->assertSame('first_name', $strategy->convert('firstName'));
        $this->assertSame('email_address', $strategy->convert('emailAddress'));
        $this->assertSame('id', $strategy->convert('id'));
        $this->assertSame('user_id', $strategy->convert('userId'));
    }
}
