<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

final class PngResult extends AbstractResult
{
    private $image;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getString(): string
    {
        ob_start();
        imagepng($this->image);

        return ob_get_clean();
    }
}
