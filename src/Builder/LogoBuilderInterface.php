<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\LogoInterface;

interface LogoBuilderInterface
{
    public function path(string $path): self;
    public function build(): LogoInterface;
}
