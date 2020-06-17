<?php

declare(strict_types=1);

namespace Endroid\QrCode;

class Logo implements LogoInterface
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }
}
