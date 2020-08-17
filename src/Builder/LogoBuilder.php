<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Logo;

class LogoBuilder implements LogoBuilderInterface
{
    private string $path;

    public function path(string $path): self
    {
        $this->path = $path;
    }

    public function build(): Logo
    {
        return new Logo($this->path);
    }
}
