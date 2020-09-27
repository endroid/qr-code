<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

final class EpsResult extends AbstractResult
{
    public function getString(): string
    {
        // @todo implement EPS result

        return 'to be implemented';
    }

    public function getMimeType(): string
    {
        return 'image/eps';
    }
}
