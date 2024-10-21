<?php

declare(strict_types=1);

namespace Endroid\QrCode\Matrix;

use Endroid\QrCode\RoundBlockSizeMode;

final readonly class Matrix implements MatrixInterface
{
    private float $blockSize;
    private int $innerSize;
    private int $outerSize;
    private int $marginLeft;
    private int $marginRight;

    /** @param array<array<int>> $blockValues */
    public function __construct(
        private array $blockValues,
        int $size,
        int $margin,
        RoundBlockSizeMode $roundBlockSizeMode,
    ) {
        $blockSize = $size / $this->getBlockCount();
        $innerSize = $size;
        $outerSize = $size + 2 * $margin;

        switch ($roundBlockSizeMode) {
            case RoundBlockSizeMode::Enlarge:
                $blockSize = intval(ceil($blockSize));
                $innerSize = intval($blockSize * $this->getBlockCount());
                $outerSize = $innerSize + 2 * $margin;
                break;
            case RoundBlockSizeMode::Shrink:
                $blockSize = intval(floor($blockSize));
                $innerSize = intval($blockSize * $this->getBlockCount());
                $outerSize = $innerSize + 2 * $margin;
                break;
            case RoundBlockSizeMode::Margin:
                $blockSize = intval(floor($blockSize));
                $innerSize = intval($blockSize * $this->getBlockCount());
                break;
        }

        if ($blockSize < 1) {
            throw new \Exception('Too much data: increase image dimensions or lower error correction level');
        }

        $this->blockSize = $blockSize;
        $this->innerSize = $innerSize;
        $this->outerSize = $outerSize;
        $this->marginLeft = intval(($this->outerSize - $this->innerSize) / 2);
        $this->marginRight = $this->outerSize - $this->innerSize - $this->marginLeft;
    }

    public function getBlockValue(int $rowIndex, int $columnIndex): int
    {
        return $this->blockValues[$rowIndex][$columnIndex];
    }

    public function getBlockCount(): int
    {
        return count($this->blockValues[0]);
    }

    public function getBlockSize(): float
    {
        return $this->blockSize;
    }

    public function getInnerSize(): int
    {
        return $this->innerSize;
    }

    public function getOuterSize(): int
    {
        return $this->outerSize;
    }

    public function getMarginLeft(): int
    {
        return $this->marginLeft;
    }

    public function getMarginRight(): int
    {
        return $this->marginRight;
    }
}
