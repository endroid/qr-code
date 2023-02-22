<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

final class GifResult extends AbstractGdResult
{
    public function getString(): string
    {
        ob_start();
        imagegif($this->image);

        return (string) ob_get_clean();
    }

    public function getMimeType(): string
    {
        return 'image/gif';
    }
}
