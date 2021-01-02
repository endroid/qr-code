<?php

declare(strict_types=1);

namespace Endroid\QrCode\Matrix;

interface MatrixInterface
{
    /** @return array<array<int>> */
    public function getBlockValues(): array;

    public function getBlockCount(): int;

    public function getBlockSize(): float;

    public function getInnerSize(): int;

    public function getOuterSize(): int;

    public function getMarginLeft(): int;

    public function getMarginRight(): int;
}
