<?php

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\PdfResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final class PdfWriter implements WriterInterface, LabelWriterInterface, LogoWriterInterface
{
    public const WRITER_OPTION_UNIT = 'unit';

    public function writeQrCode(QrCodeInterface $qrCode, array $options = []): ResultInterface
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

        $data = $qrCode->getData();

        // @todo Check how to add label height later
        $fpdf = new \FPDF('P', $unit, [$matrix->getOuterSize(), $matrix->getOuterSize()]);
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

        return new PdfResult($fpdf);
    }

    public function writeLabel(LabelInterface $label, ResultInterface $result, array $options = []): ResultInterface
    {
        $label = $qrCode->getLabel();
        $labelHeight = $label !== null ? 30 : 0;

        if (null !== $label) {
            $fpdf->setY($data['outer_height'] + 5);
            $fpdf->SetFont('Helvetica', null, $qrCode->getLabelFontSize());
            $fpdf->Cell(0, 0, $label, 0, 0, strtoupper($qrCode->getLabelAlignment()[0]));
        }
    }

    public function writeLogo(LogoInterface $logo, ResultInterface $result, array $options = []): ResultInterface
    {
        $logoPath = $qrCode->getLogoPath();
        if (null !== $logoPath) {
            $this->addLogo(
                $fpdf,
                $logoPath,
                $qrCode->getLogoWidth(),
                $qrCode->getLogoHeight(),
                $data['outer_width'],
                $data['outer_height']
            );
        }
    }
}
