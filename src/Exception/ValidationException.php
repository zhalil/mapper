<?php

namespace Zhalil\Mapper\Exception;

use RuntimeException;
use Zhalil\Mapper\ErrorBag;

final class ValidationException extends RuntimeException
{
    private string $className;

    public function __construct(
        private readonly ErrorBag $errorBag,
        string $className = 'Unknown'
    ) {
        $this->className = $className;
        parent::__construct($this->buildMessage());
    }

    public function errors(): ErrorBag
    {
        return $this->errorBag;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    private function buildMessage(): string
    {
        $errorCount = $this->errorBag->count();
        $plural = $errorCount === 1 ? 'error' : 'errors';

        $message = sprintf(
            "Validation failed with %d %s:\n\n%s",
            $errorCount,
            $plural,
            $this->formatErrors()
        );

        return $message;
    }

    private function formatErrors(): string
    {
        $lines = [];
        foreach ($this->errorBag->all() as $property => $messages) {
            foreach ($messages as $message) {
                $lines[] = sprintf("  • %s: %s", $property, $message);
            }
        }
        return implode("\n", $lines);
    }

    public function __toString(): string
    {
        return $this->getMessage();
    }
}
