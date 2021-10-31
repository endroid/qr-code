<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label\Margin;

final class Margin implements MarginInterface
{
    private int $top;
    private int $right;
    private int $bottom;
    private int $left;

    public function __construct(int $top, int $right, int $bottom, int $left)
    {
        $this->top = $top;
        $this->right = $right;
        $this->bottom = $bottom;
        $this->left = $left;
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
