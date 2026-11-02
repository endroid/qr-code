<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Matrix\MatrixInterface;

final class PdfResult extends AbstractResult
{
    public function __construct(
        MatrixInterface $matrix,
        private readonly \FPDF $fpdf,
    ) {
        parent::__construct($matrix);
    }

    public function getPdf(): \FPDF
    {
        return $this->fpdf;
    }

    #[\Override]
    public function getString(): string
    {
        /** @var mixed $output FPDF has no declared return type; destination S must return document data. */
        $output = $this->fpdf->Output('S');
        if (!is_string($output)) {
            throw new \TypeError('FPDF did not return document data');
        }

        return $output;
    }

    #[\Override]
    public function getMimeType(): string
    {
        return 'application/pdf';
    }
}
