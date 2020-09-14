<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

interface QrCodeBuilderFactoryInterface
{
    public function create(): QrCodeBuilderInterface;
}
