<?php

declare(strict_types=1);

namespace Endroid\QrCode\Enum;

abstract class AbstractEnum
{
    private $value;

    private function __construct(string $value)
    {
        $this->assertValid($value);

        $this->value = $value;
    }

    public static function create(string $value): self
    {
        return new static($value);
    }

    public static function values(): array
    {
        $reflectionClass = new \ReflectionClass(get_called_class());

        return $reflectionClass->getConstants();
    }

    private function assertValid(string $value): void
    {
        foreach ($this->values() as $constantName => $constantValue) {
            if ($value === $constantValue) {
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('Invalid ErrorCorrectionLevel value "%s": choose one of "%s"', $value, implode(', ', array_values($constants))));
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
