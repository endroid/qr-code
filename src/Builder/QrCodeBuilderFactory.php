<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

class QrCodeBuilderFactory implements QrCodeBuilderFactoryInterface
{
    public function create(): QrCodeBuilder
    {
        return new QrCodeBuilder();
    }
}
