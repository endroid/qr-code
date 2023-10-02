<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Encoding\EncodingInterface;

final class QrCode implements QrCodeInterface
{
    public function __construct(
        private string $data,
        private EncodingInterface $encoding = new Encoding('UTF-8'),
        private ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::Low,
        private int $size = 300,
        private int $margin = 10,
        private RoundBlockSizeMode $roundBlockSizeMode = RoundBlockSizeMode::Margin,
        private ColorInterface $foregroundColor = new Color(0, 0, 0),
        private ColorInterface $backgroundColor = new Color(255, 255, 255)
    ) {
    }

    public static function create(string $data): self
    {
        return new self($data);
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getEncoding(): EncodingInterface
    {
        return $this->encoding;
    }

    public function setEncoding(Encoding $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    public function getErrorCorrectionLevel(): ErrorCorrectionLevel
    {
        return $this->errorCorrectionLevel;
    }

    public function setErrorCorrectionLevel(ErrorCorrectionLevel $errorCorrectionLevel): self
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getMargin(): int
    {
        return $this->margin;
    }

    public function setMargin(int $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    public function getRoundBlockSizeMode(): RoundBlockSizeMode
    {
        return $this->roundBlockSizeMode;
    }

    public function setRoundBlockSizeMode(RoundBlockSizeMode $roundBlockSizeMode): self
    {
        $this->roundBlockSizeMode = $roundBlockSizeMode;

        return $this;
    }

    public function getForegroundColor(): ColorInterface
    {
        return $this->foregroundColor;
    }

    public function setForegroundColor(ColorInterface $foregroundColor): self
    {
        $this->foregroundColor = $foregroundColor;

        return $this;
    }

    public function getBackgroundColor(): ColorInterface
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(ColorInterface $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }
}
