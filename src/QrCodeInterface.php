<?php

declare(strict_types=1);

namespace Endroid\QrCode;

interface QrCodeInterface
{
    public function getData(): string;

    public function getEncoding(): EncodingInterface;

    public function getErrorCorrectionLevel(): ErrorCorrectionLevelInterface;
}
