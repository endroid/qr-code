<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\PdfResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final readonly class PdfWriter implements WriterInterface
{
    public const string WRITER_OPTION_UNIT = 'unit';
    public const string WRITER_OPTION_PDF = 'fpdf';
    public const string WRITER_OPTION_X = 'x';
    public const string WRITER_OPTION_Y = 'y';
    public const string WRITER_OPTION_LINK = 'link';

    #[\Override]
    public function write(
        QrCodeInterface $qrCode,
        ?LogoInterface $logo = null,
        ?LabelInterface $label = null,
        array $options = [],
    ): ResultInterface {
        /** @var array{unit?: string|null, fpdf?: mixed, x?: int|float|null, y?: int|float|null, link?: string|int|null} $options */
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $unit = $options[self::WRITER_OPTION_UNIT] ?? 'mm';

        $allowedUnits = ['mm', 'pt', 'cm', 'in'];
        if (!in_array($unit, $allowedUnits, strict: true)) {
            throw new \Exception(sprintf('PDF Measure unit should be one of [%s]', implode(', ', $allowedUnits)));
        }

        $labelSpace = 0;
        if ($label instanceof LabelInterface) {
            $labelSpace = 30;
        }

        if (!class_exists(\FPDF::class)) {
            throw new \Exception('Unable to find FPDF: check your installation');
        }

        $foregroundColor = $qrCode->getForegroundColor();
        if ($foregroundColor->getAlpha() > 0) {
            throw new \Exception('PDF Writer does not support alpha channels');
        }
        $backgroundColor = $qrCode->getBackgroundColor();
        if ($backgroundColor->getAlpha() > 0) {
            throw new \Exception('PDF Writer does not support alpha channels');
        }

        $fpdf = $this->createPdf($options[self::WRITER_OPTION_PDF] ?? null, $unit, $matrix, $labelSpace);
        $x = $options[self::WRITER_OPTION_X] ?? 0;
        $y = $options[self::WRITER_OPTION_Y] ?? 0;

        $fpdf->SetFillColor($backgroundColor->getRed(), $backgroundColor->getGreen(), $backgroundColor->getBlue());
        $fpdf->Rect($x, $y, $matrix->getOuterSize(), $matrix->getOuterSize(), 'F');
        $fpdf->SetFillColor($foregroundColor->getRed(), $foregroundColor->getGreen(), $foregroundColor->getBlue());

        $this->drawMatrix($fpdf, $matrix, $x, $y);

        if ($logo instanceof LogoInterface) {
            $this->addLogo($logo, $fpdf, $x, $y, $matrix->getOuterSize());
        }

        if ($label instanceof LabelInterface) {
            $this->addLabel(
                $label,
                $fpdf,
                $x,
                $y + $matrix->getOuterSize() + $labelSpace - 25,
                $matrix->getOuterSize(),
            );
        }

        if (null !== ($options[self::WRITER_OPTION_LINK] ?? null)) {
            $fpdf->Link(
                $x,
                $y,
                $x + $matrix->getOuterSize(),
                $y + $matrix->getOuterSize(),
                $options[self::WRITER_OPTION_LINK],
            );
        }

        return new PdfResult($matrix, $fpdf);
    }

    private function createPdf(mixed $pdf, string $unit, MatrixInterface $matrix, int $labelSpace): \FPDF
    {
        if (null !== $pdf) {
            if (!$pdf instanceof \FPDF) {
                throw new \Exception('pdf option must be an instance of FPDF');
            }

            return $pdf;
        }

        // @todo Check how to add label height later
        $pdf = new \FPDF('P', $unit, [$matrix->getOuterSize(), $matrix->getOuterSize() + $labelSpace]);
        $pdf->AddPage();

        return $pdf;
    }

    private function drawMatrix(\FPDF $fpdf, MatrixInterface $matrix, float $x, float $y): void
    {
        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 !== $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    continue;
                }

                $fpdf->Rect(
                    $x + $matrix->getMarginLeft() + ($columnIndex * $matrix->getBlockSize()),
                    $y + $matrix->getMarginLeft() + ($rowIndex * $matrix->getBlockSize()),
                    $matrix->getBlockSize(),
                    $matrix->getBlockSize(),
                    'F',
                );
            }
        }
    }

    private function addLabel(LabelInterface $label, \FPDF $fpdf, float $x, float $y, float $width): void
    {
        $fpdf->SetXY($x, $y);
        $fpdf->SetFont('Helvetica', '', $label->getFont()->getSize());
        $fpdf->Cell($width, 0, $label->getText(), 0, 0, 'C');
    }

    private function addLogo(LogoInterface $logo, \FPDF $fpdf, float $x, float $y, float $size): void
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

        $logoX = $x + ($size / 2) - ($logoWidth / 2);
        $logoY = $y + ($size / 2) - ($logoHeight / 2);

        $fpdf->Image($logoPath, $logoX, $logoY, $logoWidth, $logoHeight);
    }
}
