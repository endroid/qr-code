<?php

declare(strict_types=1);

namespace Endroid\QrCode\QrCode;

use Endroid\QrCode\QrCode\Encoding\EncodingInterface;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;

interface QrCodeInterface
{
    public function getData(): string;

    public function getEncoding(): EncodingInterface;

    public function getErrorCorrectionLevel(): ErrorCorrectionLevelInterface;
}
