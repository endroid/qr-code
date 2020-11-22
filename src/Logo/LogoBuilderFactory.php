<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

final class LogoBuilderFactory implements LogoBuilderFactoryInterface
{
    public function create(): LogoBuilder
    {
        return new LogoBuilder();
    }
}
