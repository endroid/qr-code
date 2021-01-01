<?php

declare(strict_types=1);

namespace Endroid\QrCode;

final class QrCodeBuilderFactory implements QrCodeBuilderFactoryInterface
{
    public function create(): QrCodeBuilder
    {
        return new QrCodeBuilder();
    }
}
