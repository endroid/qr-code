<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\EncodingInterface;
use Endroid\QrCode\ErrorCorrectionLevelInterface;
use Endroid\QrCode\QrCode;

class QrCodeBuilder implements QrCodeBuilderInterface
{
    private string $data;
    private EncodingInterface $encoding;
    private ErrorCorrectionLevelInterface $errorCorrectionLevel;

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

    public function getResult(): QrCode
    {
        return new QrCode($this->data, $this->encoding, $this->errorCorrectionLevel);
    }
}
