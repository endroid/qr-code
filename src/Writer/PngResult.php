<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

final class PngResult extends AbstractResult
{
    private $image;

    public function getMimeType(): string
    {
        return 'image/png';
    }

    public function getString(): string
    {
        // TODO: Implement getString() method.
    }
}
