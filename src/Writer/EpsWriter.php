<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\QrCodeInterface;

final class EpsWriter implements WriterInterface
{
    public function writeQrCode(QrCodeInterface $qrCode): ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $lines = [
            '%!PS-Adobe-3.0 EPSF-3.0',
            '%%BoundingBox: 0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize(),
            '/F { rectfill } def',
            number_format($qrCode->getBackgroundColor()->getRed() / 100, 2, '.', ',').' '.number_format($qrCode->getBackgroundColor()->getGreen() / 100, 2, '.', ',').' '.number_format($qrCode->getBackgroundColor()->getBlue() / 100, 2, '.', ',').' setrgbcolor',
            '0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize().' F',
            number_format($qrCode->getForegroundColor()->getRed() / 100, 2, '.', ',').' '.number_format($qrCode->getForegroundColor()->getGreen() / 100, 2, '.', ',').' '.number_format($qrCode->getForegroundColor()->getBlue() / 100, 2, '.', ',').' setrgbcolor',
        ];

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($matrix->getBlockCount() - 1 - $rowIndex, $columnIndex)) {
                    $x = $matrix->getMarginLeft() + $matrix->getBlockSize() * $columnIndex;
                    $y = $matrix->getMarginLeft() + $matrix->getBlockSize() * $rowIndex;
                    $lines[] = $x.' '.$y.' '.$matrix->getBlockSize().' '.$matrix->getBlockSize().' F';
                }
            }
        }

        return new EpsResult($lines);
    }
}
