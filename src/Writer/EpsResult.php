<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

final class EpsResult extends AbstractResult
{
    /** @var array<string> */
    private $lines;

    /** @param array<string> $lines */
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public function getMimeType(): string
    {
        return 'image/eps';
    }

    public function getString(): string
    {
        return implode("\n", $this->lines);
    }
}
