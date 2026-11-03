<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

final readonly class Logo implements LogoInterface
{
    public function __construct(
        private string $path,
        private ?int $resizeToWidth = null,
        private ?int $resizeToHeight = null,
        private bool $punchoutBackground = false,
    ) {}

    #[\Override]
    public function getPath(): string
    {
        return $this->path;
    }

    #[\Override]
    public function getResizeToWidth(): ?int
    {
        return $this->resizeToWidth;
    }

    #[\Override]
    public function getResizeToHeight(): ?int
    {
        return $this->resizeToHeight;
    }

    #[\Override]
    public function getPunchoutBackground(): bool
    {
        return $this->punchoutBackground;
    }
}
