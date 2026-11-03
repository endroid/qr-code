<?php

declare(strict_types=1);

namespace Endroid\QrCode\Matrix;

use Endroid\QrCode\Exception\BlockSizeTooSmallException;
use Endroid\QrCode\RoundBlockSizeMode;

final readonly class Matrix implements MatrixInterface
{
    private float $blockSize;
    private int $innerSize;
    private int $outerSize;
    private int $marginLeft;
    private int $marginRight;

    /** @param array<array<int<0, 1>>> $blockValues */
    public function __construct(
        private array $blockValues,
        int $size,
        int $margin,
        RoundBlockSizeMode $roundBlockSizeMode,
    ) {
        $blockSize = $size / $this->getBlockCount();
        $innerSize = $size;
        $outerSize = $size + (2 * $margin);

        switch ($roundBlockSizeMode) {
            case RoundBlockSizeMode::Enlarge:
                $blockSize = intval(ceil($blockSize));
                $innerSize = intval($blockSize * $this->getBlockCount());
                $outerSize = $innerSize + (2 * $margin);
                break;
            case RoundBlockSizeMode::Shrink:
                $blockSize = intval(floor($blockSize));
                $innerSize = intval($blockSize * $this->getBlockCount());
                $outerSize = $innerSize + (2 * $margin);
                break;
            case RoundBlockSizeMode::Margin:
                $blockSize = intval(floor($blockSize));
                $innerSize = intval($blockSize * $this->getBlockCount());
                break;
        }

        if ($blockSize < 1) {
            throw new BlockSizeTooSmallException(
                'Too much data: increase image dimensions or lower error correction level',
            );
        }

        $this->blockSize = $blockSize;
        $this->innerSize = $innerSize;
        $this->outerSize = $outerSize;
        $this->marginLeft = intval(($this->outerSize - $this->innerSize) / 2);
        $this->marginRight = $this->outerSize - $this->innerSize - $this->marginLeft;
    }

    /** @return int<0, 1> */
    #[\Override]
    public function getBlockValue(int $rowIndex, int $columnIndex): int
    {
        return (
            $this->blockValues[$rowIndex][$columnIndex] ?? throw new \TypeError('Matrix coordinates are out of bounds')
        );
    }

    /** @return non-negative-int */
    #[\Override]
    public function getBlockCount(): int
    {
        return count($this->blockValues[0] ?? throw new \TypeError('Matrix has no first row'));
    }

    #[\Override]
    public function getBlockSize(): float
    {
        return $this->blockSize;
    }

    #[\Override]
    public function getInnerSize(): int
    {
        return $this->innerSize;
    }

    #[\Override]
    public function getOuterSize(): int
    {
        return $this->outerSize;
    }

    #[\Override]
    public function getMarginLeft(): int
    {
        return $this->marginLeft;
    }

    #[\Override]
    public function getMarginRight(): int
    {
        return $this->marginRight;
    }
}
