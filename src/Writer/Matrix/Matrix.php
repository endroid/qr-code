<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Matrix;

use BaconQrCode\Encoder\Encoder;
use Endroid\QrCode\QrCode\QrCodeInterface;

class Matrix
{
    private array $blockValues = [];

    public function __construct(QrCodeInterface $qrCode)
    {
        $baconErrorCorrectionLevel = ErrorCorrectionLevelConverter::convertToBaconErrorCorrectionLevel($qrCode->getErrorCorrectionLevel());
        $baconMatrix = Encoder::encode($qrCode->getData(), $baconErrorCorrectionLevel, strval($qrCode->getEncoding()))->getMatrix();

        $columnCount = $baconMatrix->getWidth();
        $rowCount = $baconMatrix->getHeight();
        for ($rowIndex = 0; $rowIndex < $rowCount; ++$rowIndex) {
            $this->blockValues[$rowIndex] = [];
            for ($columnIndex = 0; $columnIndex < $columnCount; ++$columnIndex) {
                $this->blockValues[$rowIndex][$columnIndex] = $baconMatrix->get($columnIndex, $rowIndex);
            }
        }
    }

    public function getBlockCount(): int
    {
        return count($this->blockValues[0]);
    }

    public function getBlockValue(int $rowIndex, int $columnIndex): int
    {
        return $this->blockValues[$rowIndex][$columnIndex];
    }
}
