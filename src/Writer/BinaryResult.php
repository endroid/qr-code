<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Matrix\MatrixInterface;

final class BinaryResult extends AbstractResult
{
    private $matrix;

    public function __construct(MatrixInterface $matrix)
    {
        $this->matrix = $matrix;
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }

    public function getString(): string
    {
        $rows = array_map('implode', $this->matrix->getBlockValues());

        return implode("\n", $rows);
    }
}
