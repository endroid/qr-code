<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

class PdfWriter extends AbstractWriter
{
    public function writeString(): string
    {
    }

    public function getMimeType(): string
    {
        return 'application/pdf';
    }
}
