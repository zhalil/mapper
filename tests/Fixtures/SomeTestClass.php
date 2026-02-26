<?php

namespace Zhalil\Mapper\Tests\Fixtures;

use Zhalil\Mapper\Attribute\Strict;

#[Strict]
class SomeTestClass {
    /**
     * @param string $test
     * @param SomeItem[] $items
     */
    public function __construct(
        private string $test,
        private array $items
    ) {}
    
    public function getTest(): string { return $this->test; }
    public function getItems(): array { return $this->items; }
}
