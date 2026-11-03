<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label\Margin;

final readonly class Margin implements MarginInterface
{
    public function __construct(
        private int $top,
        private int $right,
        private int $bottom,
        private int $left,
    ) {}

    #[\Override]
    public function getTop(): int
    {
        return $this->top;
    }

    #[\Override]
    public function getRight(): int
    {
        return $this->right;
    }

    #[\Override]
    public function getBottom(): int
    {
        return $this->bottom;
    }

    #[\Override]
    public function getLeft(): int
    {
        return $this->left;
    }

    /** @return array<string, int> */
    #[\Override]
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
