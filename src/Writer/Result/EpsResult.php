<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Matrix\MatrixInterface;

final class EpsResult extends AbstractResult
{
    /** @var array<string> */
    private array $lines;

    /** @param array<string> $lines */
    public function __construct(MatrixInterface $matrix, array $lines)
    {
        parent::__construct($matrix);

        $this->lines = $lines;
    }

    public function getString(): string
    {
        return implode("\n", $this->lines);
    }

    public function getMimeType(): string
    {
        return 'image/eps';
    }
}
