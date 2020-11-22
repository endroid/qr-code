<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

interface LogoBuilderFactoryInterface
{
    public function create(): LogoBuilderInterface;
}
