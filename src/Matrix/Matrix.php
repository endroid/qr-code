<?php

declare(strict_types=1);

namespace Endroid\QrCode\Matrix;

use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeEnlarge;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeInterface;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeShrink;

class Matrix implements MatrixInterface
{
    /** @var array<array<int>> */
    private $blockValues = [];

    /** @var float */
    private $blockSize;

    /** @var int */
    private $innerSize;

    /** @var int */
    private $outerSize;

    /** @var int */
    private $marginLeft;

    /** @var int */
    private $marginRight;

    /** @param array<array<int>> $blockValues */
    public function __construct(array $blockValues, int $size, int $margin, RoundBlockSizeModeInterface $roundBlockSizeMode)
    {
        $this->blockValues = $blockValues;

        $this->blockSize = $size / $this->getBlockCount();
        $this->innerSize = $size;
        $this->outerSize = $size + 2 * $margin;

        if ($roundBlockSizeMode instanceof RoundBlockSizeModeEnlarge) {
            $this->blockSize = intval(ceil($this->blockSize));
            $this->innerSize = $this->blockSize * $this->getBlockCount();
            $this->outerSize = $this->innerSize + 2 * $margin;
        } elseif ($roundBlockSizeMode instanceof RoundBlockSizeModeShrink) {
            $this->blockSize = intval(floor($this->blockSize));
            $this->innerSize = $this->blockSize * $this->getBlockCount();
            $this->outerSize = $this->innerSize + 2 * $margin;
        } elseif ($roundBlockSizeMode instanceof RoundBlockSizeModeMargin) {
            $this->blockSize = intval(floor($this->blockSize));
            $this->innerSize = $this->blockSize * $this->getBlockCount();
        }

        $this->marginLeft = intval(($this->outerSize - $this->innerSize) / 2);
        $this->marginRight = $this->outerSize - $this->innerSize - $this->marginLeft;
    }

    public function getBlockValues(): array
    {
        return $this->blockValues;
    }

    public function getBlockCount(): int
    {
        return count($this->blockValues[0]);
    }

    public function getBlockValue(int $rowIndex, int $columnIndex): int
    {
        return $this->blockValues[$rowIndex][$columnIndex];
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
