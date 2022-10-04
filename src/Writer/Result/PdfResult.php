<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Matrix\MatrixInterface;

final class PdfResult extends AbstractResult
{
    private \FPDF $fpdf;

    public function __construct(MatrixInterface $matrix, \FPDF $fpdf)
    {
        parent::__construct($matrix);

        $this->fpdf = $fpdf;
    }

    public function getPdf(): \FPDF
    {
        return $this->fpdf;
    }

    public function getString(): string
    {
        return $this->fpdf->Output('S');
    }

    public function getMimeType(): string
    {
        return 'application/pdf';
    }
}
