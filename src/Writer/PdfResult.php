<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

final class PdfResult extends AbstractResult
{
    public function getString(): string
    {
        // @todo generate PDF here?

        return '';
    }

    public function getMimeType(): string
    {
        return 'application/pdf';
    }
}
