<?php

declare(strict_types=1);

namespace Endroid\QrCode\QrCode;

interface QrCodeBuilderFactoryInterface
{
    public function create(): QrCodeBuilderInterface;
}
