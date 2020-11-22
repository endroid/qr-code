<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Matrix\ImageMatrix;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeInterface;

trait ImageWriterTrait
{
    private int $size;
    private int $margin;
    private RoundBlockSizeModeInterface $roundBlockSizeMode;

    public function __construct(int $size, int $margin, RoundBlockSizeModeInterface $roundBlockSizeMode)
    {
        $this->size = $size;
        $this->margin = $margin;
        $this->roundBlockSizeMode = $roundBlockSizeMode;
    }

    public function getMatrix(QrCodeInterface $qrCode): ImageMatrix
    {
        return new ImageMatrix($qrCode, $this->size, $this->margin, $this->roundBlockSizeMode);
    }
}
