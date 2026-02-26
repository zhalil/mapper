<?php

namespace Zhalil\Mapper\Tests;

use PHPUnit\Framework\TestCase;
use Zhalil\Mapper\ErrorBag;

class ErrorBagTest extends TestCase
{
    public function testAddAndGetErrors(): void
    {
        $bag = new ErrorBag();
        $bag->add('name', 'Name is required');
        $bag->add('name', 'Name must be at least 2 characters');

        $this->assertTrue($bag->has('name'));
        $this->assertFalse($bag->has('email'));
        $this->assertSame(['Name is required', 'Name must be at least 2 characters'], $bag->get('name'));
    }

    public function testAllReturnsAllErrors(): void
    {
        $bag = new ErrorBag();
        $bag->add('name', 'Name is required');
        $bag->add('email', 'Invalid email');

        $this->assertSame([
            'name' => ['Name is required'],
            'email' => ['Invalid email'],
        ], $bag->all());
    }

    public function testToArrayReturnsNestedArray(): void
    {
        $bag = new ErrorBag();
        $bag->add('name', 'Name is required');
        $bag->add('name', 'Name too short');
        $bag->add('email', 'Invalid email');

        $this->assertSame([
            'name' => ['Name is required', 'Name too short'],
            'email' => ['Invalid email'],
        ], $bag->toArray());
    }

    public function testToJsonReturnsJson(): void
    {
        $bag = new ErrorBag();
        $bag->add('name', 'Name is required');
        $bag->add('email', 'Invalid email');

        $json = $bag->toJson();
        $this->assertStringContainsString('"name"', $json);
        $this->assertStringContainsString('"Name is required"', $json);
        $this->assertStringContainsString('"email"', $json);
        $this->assertStringContainsString('"Invalid email"', $json);
    }

    public function testIsEmptyAndCount(): void
    {
        $bag = new ErrorBag();
        $this->assertTrue($bag->isEmpty());
        $this->assertSame(0, $bag->count());

        $bag->add('name', 'Error');
        $this->assertFalse($bag->isEmpty());
        $this->assertSame(1, $bag->count());

        $bag->add('name', 'Another error');
        $this->assertSame(2, $bag->count());
    }
}
