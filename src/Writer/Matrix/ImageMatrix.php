<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Matrix;

use Endroid\QrCode\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\RoundBlockSizeMode\Enlarge;
use Endroid\QrCode\Writer\RoundBlockSizeMode\Margin;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeInterface;
use Endroid\QrCode\Writer\RoundBlockSizeMode\Shrink;

final class ImageMatrix extends Matrix
{
    private float $blockSize;
    private int $innerSize;
    private int $outerSize;
    private int $marginLeft;
    private int $marginRight;

    public function __construct(QrCodeInterface $qrCode, int $size, int $margin, RoundBlockSizeModeInterface $roundBlockSizeMode)
    {
        parent::__construct($qrCode);

        $this->blockSize = $size / $this->getBlockCount();
        $this->innerSize = $size;
        $this->outerSize = $size + 2 * $margin;

        if ($roundBlockSizeMode instanceof Enlarge) {
            $this->blockSize = intval(ceil($this->blockSize));
            $this->innerSize = $this->blockSize * $this->getBlockCount();
            $this->outerSize = $this->innerSize + 2 * $margin;
        } elseif ($roundBlockSizeMode instanceof Shrink) {
            $this->blockSize = intval(floor($this->blockSize));
            $this->innerSize = $this->blockSize * $this->getBlockCount();
            $this->outerSize = $this->innerSize + 2 * $margin;
        } elseif ($roundBlockSizeMode instanceof Margin) {
            $this->blockSize = intval(floor($this->blockSize));
            $this->innerSize = $this->blockSize * $this->getBlockCount();
        }

        $this->marginLeft = intval(($this->outerSize - $this->innerSize) / 2);
        $this->marginRight = $this->outerSize - $this->innerSize - $this->marginLeft;
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
