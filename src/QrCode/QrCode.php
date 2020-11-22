<?php

declare(strict_types=1);

namespace Endroid\QrCode\QrCode;

use Endroid\QrCode\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode\Encoding\EncodingInterface;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\Low;

final class QrCode implements QrCodeInterface
{
    private string $data;
    private EncodingInterface $encoding;
    private ErrorCorrectionLevelInterface $errorCorrectionLevel;

    public function __construct(
        string $data,
        EncodingInterface $encoding = null,
        ErrorCorrectionLevelInterface $errorCorrectionLevel = null
    ) {
        $this->data = $data;
        $this->encoding = isset($encoding) ? $encoding : new Encoding('UTF-8');
        $this->errorCorrectionLevel = isset($errorCorrectionLevel) ? $errorCorrectionLevel : new Low();
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getEncoding(): EncodingInterface
    {
        return $this->encoding;
    }

    public function getErrorCorrectionLevel(): ErrorCorrectionLevelInterface
    {
        return $this->errorCorrectionLevel;
    }
}
