<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Encoding;
use Endroid\QrCode\ErrorCorrectionLevelInterface;
use Endroid\QrCode\QrCodeInterface;

interface QrCodeBuilderInterface
{
    public function data(string $data): self;
    public function encoding(Encoding $encoding): self;
    public function errorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): self;
    public function getResult(): QrCodeInterface;
}
