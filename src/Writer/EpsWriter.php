<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\EpsResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final readonly class EpsWriter implements WriterInterface
{
    public const int DECIMAL_PRECISION = 10;

    #[\Override]
    public function write(
        QrCodeInterface $qrCode,
        ?LogoInterface $logo = null,
        ?LabelInterface $label = null,
        array $options = [],
    ): ResultInterface {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $lines = [
            '%!PS-Adobe-3.0 EPSF-3.0',
            '%%BoundingBox: 0 0 ' . $matrix->getOuterSize() . ' ' . $matrix->getOuterSize(),
            '/F { rectfill } def',
            $this->formatColor($qrCode->getBackgroundColor()) . ' setrgbcolor',
            '0 0 ' . $matrix->getOuterSize() . ' ' . $matrix->getOuterSize() . ' F',
            $this->formatColor($qrCode->getForegroundColor()) . ' setrgbcolor',
        ];

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 !== $matrix->getBlockValue($matrix->getBlockCount() - 1 - $rowIndex, $columnIndex)) {
                    continue;
                }

                $x = $matrix->getMarginLeft() + ($matrix->getBlockSize() * $columnIndex);
                $y = $matrix->getMarginLeft() + ($matrix->getBlockSize() * $rowIndex);
                $lines[] =
                    number_format($x, self::DECIMAL_PRECISION, decimal_separator: '.', thousands_separator: '')
                    . ' '
                    . number_format($y, self::DECIMAL_PRECISION, decimal_separator: '.', thousands_separator: '')
                    . ' '
                    . number_format(
                        $matrix->getBlockSize(),
                        self::DECIMAL_PRECISION,
                        decimal_separator: '.',
                        thousands_separator: '',
                    )
                    . ' '
                    . number_format(
                        $matrix->getBlockSize(),
                        self::DECIMAL_PRECISION,
                        decimal_separator: '.',
                        thousands_separator: '',
                    )
                    . ' F';
            }
        }

        return new EpsResult($matrix, $lines);
    }

    private function formatColor(ColorInterface $color): string
    {
        return (
            number_format($color->getRed() / 100, decimals: 2, decimal_separator: '.', thousands_separator: ',')
            . ' '
            . number_format($color->getGreen() / 100, decimals: 2, decimal_separator: '.', thousands_separator: ',')
            . ' '
            . number_format($color->getBlue() / 100, decimals: 2, decimal_separator: '.', thousands_separator: ',')
        );
    }
}
