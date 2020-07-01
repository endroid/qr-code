<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

class EpsWriter extends AbstractWriter
{
    public function writeString(): string
    {
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }
}
