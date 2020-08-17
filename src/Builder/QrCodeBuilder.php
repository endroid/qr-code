<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;

class QrCodeBuilder implements QrCodeBuilderInterface
{
    private string $data;
    private Encoding $encoding;
    private ErrorCorrectionLevel $errorCorrectionLevel;

    public function data(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function encoding(Encoding $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    public function errorCorrectionLevel(ErrorCorrectionLevel $errorCorrectionLevel): self
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
    }

    public function build(): QrCode
    {
        return new QrCode($this->data, $this->encoding, $this->errorCorrectionLevel);
    }
}
