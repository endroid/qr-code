<?php

declare(strict_types=1);

namespace Endroid\QrCode\Color;

final class Color implements ColorInterface
{
    /** @var int */
    private $red;

    /** @var int */
    private $green;

    /** @var int */
    private $blue;

    /** @var int */
    private $alpha;

    public function __construct(int $red, int $green, int $blue, int $alpha = 0)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $alpha;
    }

    public function getRed(): int
    {
        return $this->red;
    }

    public function getGreen(): int
    {
        return $this->green;
    }

    public function getBlue(): int
    {
        return $this->blue;
    }

    public function getAlpha(): int
    {
        return $this->alpha;
    }
}
