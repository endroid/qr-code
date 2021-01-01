<?php

declare(strict_types=1);

namespace Endroid\QrCode;

interface QrCodeBuilderFactoryInterface
{
    public function create(): QrCodeBuilderInterface;
}
