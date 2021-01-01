<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Matrix\Matrix;

final class BinaryResult extends AbstractResult
{
    private $matrix;

    public function __construct(Matrix $matrix)
    {
        $this->matrix = $matrix;
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }

    public function getString(): string
    {
        return implode("\n", array_map(function (\ArrayIterator $rowIterator) {
            return implode('', $rowIterator->getArrayCopy());
        }, $this->matrix->getIterator()->getArrayCopy()));
    }
}
