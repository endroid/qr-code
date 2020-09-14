<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

interface LogoBuilderFactoryInterface
{
    public function create(): LogoBuilderInterface;
}
