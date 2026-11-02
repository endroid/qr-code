<?php

declare(strict_types=1);

namespace Endroid\QrCode\Matrix;

interface MatrixInterface
{
    /** @return int<0, 1> */
    public function getBlockValue(int $rowIndex, int $columnIndex): int;

    /** @return non-negative-int */
    public function getBlockCount(): int;

    public function getBlockSize(): float;

    public function getInnerSize(): int;

    public function getOuterSize(): int;

    public function getMarginLeft(): int;

    public function getMarginRight(): int;
}
