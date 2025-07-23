<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\EpsResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final readonly class EpsWriter implements WriterInterface
{
    public const DECIMAL_PRECISION = 10;

    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []): ResultInterface
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
                    $lines[] = number_format($x, self::DECIMAL_PRECISION, '.', '').' '.number_format($y, self::DECIMAL_PRECISION, '.', '').' '.number_format($matrix->getBlockSize(), self::DECIMAL_PRECISION, '.', '').' '.number_format($matrix->getBlockSize(), self::DECIMAL_PRECISION, '.', '').' F';
                }
            }
        }

        if ($logo instanceof LogoInterface) {
            $this->addLogo($logo, $lines, $matrix->getOuterSize());
        }

        return new EpsResult($matrix, $lines);
    }

    private function addLogo(LogoInterface $logo, array &$lines, int $outerSize): void
    {
        $logoPath = $logo->getPath();
        $logoHeight = $logo->getResizeToHeight();
        $logoWidth = $logo->getResizeToWidth();

        if (null === $logoHeight || null === $logoWidth) {
            $imageSize = \getimagesize($logoPath);
            if (!$imageSize) {
                throw new \Exception(sprintf('Unable to read image size for logo "%s"', $logoPath));
            }
            [$logoSourceWidth, $logoSourceHeight] = $imageSize;

            if (null === $logoWidth) {
                $logoWidth = (int) $logoSourceWidth;
            }

            if (null === $logoHeight) {
                $aspectRatio = $logoWidth / $logoSourceWidth;
                $logoHeight = (int) ($logoSourceHeight * $aspectRatio);
            }
        }

        $logoX = $outerSize / 2 - $logoWidth / 2;
        $logoY = $outerSize / 2 - $logoHeight / 2;

        $lines[] = 'gsave';
        $lines[] = number_format($logoX, self::DECIMAL_PRECISION, '.', '').' '.number_format($logoY, self::DECIMAL_PRECISION, '.', '').' translate';
        $lines[] = number_format($logoWidth, self::DECIMAL_PRECISION, '.', '').' '.number_format($logoHeight, self::DECIMAL_PRECISION, '.', '').' scale';
        $lines[] = '('.$logoPath.') run';
        $lines[] = 'grestore';
    }
}
