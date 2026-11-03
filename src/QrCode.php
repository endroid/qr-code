<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Encoding\EncodingInterface;

final readonly class QrCode implements QrCodeInterface
{
    public function __construct(
        private string $data,
        private EncodingInterface $encoding = new Encoding('UTF-8'),
        private ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::Low,
        private int $size = 300,
        private int $margin = 10,
        private RoundBlockSizeMode $roundBlockSizeMode = RoundBlockSizeMode::Margin,
        private ColorInterface $foregroundColor = new Color(0, 0, 0),
        private ColorInterface $backgroundColor = new Color(255, 255, 255),
    ) {}

    #[\Override]
    public function getData(): string
    {
        return $this->data;
    }

    #[\Override]
    public function getEncoding(): EncodingInterface
    {
        return $this->encoding;
    }

    #[\Override]
    public function getErrorCorrectionLevel(): ErrorCorrectionLevel
    {
        return $this->errorCorrectionLevel;
    }

    #[\Override]
    public function getSize(): int
    {
        return $this->size;
    }

    #[\Override]
    public function getMargin(): int
    {
        return $this->margin;
    }

    #[\Override]
    public function getRoundBlockSizeMode(): RoundBlockSizeMode
    {
        return $this->roundBlockSizeMode;
    }

    #[\Override]
    public function getForegroundColor(): ColorInterface
    {
        return $this->foregroundColor;
    }

    #[\Override]
    public function getBackgroundColor(): ColorInterface
    {
        return $this->backgroundColor;
    }
}
