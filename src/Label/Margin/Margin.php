<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label\Margin;

final class Margin implements MarginInterface
{
    public function __construct(
        private readonly int $top,
        private readonly int $right,
        private readonly int $bottom,
        private readonly int $left
    ) {
    }

    public function getTop(): int
    {
        return $this->top;
    }

    public function getRight(): int
    {
        return $this->right;
    }

    public function getBottom(): int
    {
        return $this->bottom;
    }

    public function getLeft(): int
    {
        return $this->left;
    }

    /** @return array<string, int> */
    public function toArray(): array
    {
        return [
            'top' => $this->top,
            'right' => $this->right,
            'bottom' => $this->bottom,
            'left' => $this->left,
        ];
    }
}
