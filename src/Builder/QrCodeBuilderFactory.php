<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\EncodingInterface;
use Endroid\QrCode\ErrorCorrectionLevelInterface;
use Endroid\QrCode\QrCode;

class QrCodeBuilderFactory implements QrCodeBuilderFactoryInterface
{
    public function create(): QrCodeBuilder
    {
        return new QrCodeBuilder();
    }
}
