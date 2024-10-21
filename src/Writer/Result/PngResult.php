<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Matrix\MatrixInterface;

final class PngResult extends GdResult
{
    public function __construct(
        MatrixInterface $matrix,
        \GdImage $image,
        private readonly int $quality = -1,
    ) {
        parent::__construct($matrix, $image);
    }

    public function getString(): string
    {
        ob_start();
        imagepng($this->image, quality: $this->quality);

        return strval(ob_get_clean());
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }
}
