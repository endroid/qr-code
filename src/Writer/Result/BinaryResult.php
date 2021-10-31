<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Matrix\MatrixInterface;

final class BinaryResult extends AbstractResult
{
    private MatrixInterface $matrix;

    public function __construct(MatrixInterface $matrix)
    {
        $this->matrix = $matrix;
    }

    public function getString(): string
    {
        $binaryString = '';
        for ($rowIndex = 0; $rowIndex < $this->matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $this->matrix->getBlockCount(); ++$columnIndex) {
                $binaryString .= $this->matrix->getBlockValue($rowIndex, $columnIndex);
            }
            $binaryString .= "\n";
        }

        return $binaryString;
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }
}
