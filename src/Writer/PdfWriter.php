<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\PdfResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final class PdfWriter implements WriterInterface
{
    public const WRITER_OPTION_UNIT = 'unit';

    public function write(QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, array $options = []): ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $unit = 'mm';
        if (isset($options[self::WRITER_OPTION_UNIT])) {
            $unit = $options[self::WRITER_OPTION_UNIT];
        }

        $allowedUnits = ['mm', 'pt', 'cm', 'in'];
        if (!in_array($unit, $allowedUnits)) {
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

        // @todo Check how to add label height later
        $fpdf = new \FPDF('P', $unit, [$matrix->getOuterSize(), $matrix->getOuterSize() + $labelSpace]);
        $fpdf->AddPage();

        $fpdf->SetFillColor($backgroundColor->getRed(), $backgroundColor->getGreen(), $backgroundColor->getBlue());
        $fpdf->Rect(0, 0, $matrix->getOuterSize(), $matrix->getOuterSize(), 'F');
        $fpdf->SetFillColor($foregroundColor->getRed(), $foregroundColor->getGreen(), $foregroundColor->getBlue());

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    $fpdf->Rect(
                        $matrix->getMarginLeft() + ($columnIndex * $matrix->getBlockSize()),
                        $matrix->getMarginLeft() + ($rowIndex * $matrix->getBlockSize()),
                        $matrix->getBlockSize(),
                        $matrix->getBlockSize(),
                        'F'
                    );
                }
            }
        }

        if ($label instanceof LabelInterface) {
            $fpdf->setY($fpdf->GetPageHeight() - 25);
            $fpdf->SetFont('Helvetica', null, $label->getFont()->getSize());
            $fpdf->Cell(0, 0, $label->getText(), 0, 0, 'C');
        }

        return new PdfResult($fpdf);
    }

//    public function writeLogo(LogoInterface $logo, ResultInterface $result, array $options = []): ResultInterface
//    {
//        $logoPath = $qrCode->getLogoPath();
//        if (null !== $logoPath) {
//            $this->addLogo(
//                $fpdf,
//                $logoPath,
//                $qrCode->getLogoWidth(),
//                $qrCode->getLogoHeight(),
//                $data['outer_width'],
//                $data['outer_height']
//            );
//        }
//
//        if (null === $logoHeight || null === $logoWidth) {
//            [$logoSourceWidth, $logoSourceHeight] = \getimagesize($logoPath);
//
//            if (null === $logoWidth) {
//                $logoWidth = (int) $logoSourceWidth;
//            }
//
//            if (null === $logoHeight) {
//                $aspectRatio = $logoWidth / $logoSourceWidth;
//                $logoHeight = (int) ($logoSourceHeight * $aspectRatio);
//            }
//        }
//
//        $logoX = $imageWidth / 2 - (int) $logoWidth / 2;
//        $logoY = $imageHeight / 2 - (int) $logoHeight / 2;
//
//        $fpdf->Image($logoPath, $logoX, $logoY, $logoWidth, $logoHeight);
//    }
}
