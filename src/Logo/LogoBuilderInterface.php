<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

interface LogoBuilderInterface
{
    public function path(string $path): self;

    public function build(): LogoInterface;
}
