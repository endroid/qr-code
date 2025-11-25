<?php

declare(strict_types=1);

namespace Endroid\QrCode\Color;

interface ColorInterface
{
    /** @return int<0, 255> */
    public function getRed(): int;

    /** @return int<0, 255> */
    public function getGreen(): int;

    /** @return int<0, 255> */
    public function getBlue(): int;

    /** @return int<0, 127> */
    public function getAlpha(): int;

    public function getOpacity(): float;

    public function getHex(): string;

    /** @return array<string, int> */
    public function toArray(): array;
}
