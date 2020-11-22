<?php

declare(strict_types=1);

namespace Endroid\QrCode\QrCode;

use Endroid\QrCode\QrCode\Encoding\EncodingInterface;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;

interface QrCodeBuilderInterface
{
    public function data(string $data): self;

    public function encoding(EncodingInterface $encoding): self;

    public function errorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): self;

    public function build(): QrCodeInterface;
}
