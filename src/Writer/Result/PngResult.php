<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

final class PngResult extends AbstractResult
{
    /** @var mixed */
    private $image;

    /** @param mixed $image */
    public function __construct($image)
    {
        $this->image = $image;
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }

    /** @return mixed */
    public function getImage()
    {
        return $this->image;
    }

    /** @param mixed $image */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function getString(): string
    {
        ob_start();
        imagepng($this->image);

        return strval(ob_get_clean());
    }
}
