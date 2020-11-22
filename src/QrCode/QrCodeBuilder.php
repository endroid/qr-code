<?php

declare(strict_types=1);

namespace Endroid\QrCode\QrCode;

use Endroid\QrCode\QrCode\Encoding\EncodingInterface;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;

final class QrCodeBuilder implements QrCodeBuilderInterface
{
    private string $data;
    private EncodingInterface $encoding;
    private ErrorCorrectionLevelInterface $errorCorrectionLevel;

    public static function create(): self
    {
        return new static();
    }

    public function data(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function encoding(EncodingInterface $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    public function errorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): self
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
    }

    public function build(): QrCode
    {
        return new QrCode($this->data, $this->encoding, $this->errorCorrectionLevel);
    }
}
