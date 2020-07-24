<?php

declare(strict_types=1);

namespace Endroid\QrCode\Enum;

abstract class AbstractEnum
{
    private string $value;

    public function __construct(string $value)
    {
        static::validateValue($value);

        $this->value = $value;
    }

    public static function validateValue(string $value): void
    {
        if (!in_array($value, static::values())) {
            throw new \InvalidArgumentException(sprintf('Invalid enum value "%s": choose one of "%s"', $value, implode(', ', static::values())));
        }
    }

    abstract public static function values(): array;
}
