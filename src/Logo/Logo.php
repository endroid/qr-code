<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

use Endroid\QrCode\Exception\QrCodeException;

final class Logo implements LogoInterface
{
    private string $path;
    private ?int $resizeWidth;

    public function __construct(string $path, int $resizeWidth = null)
    {
        $this->validatePath($path);

        $this->path = $path;
        $this->resizeWidth = $resizeWidth;
    }

    private function validatePath(string $path): void
    {
        if (!file_exists($path)) {
            throw new QrCodeException(sprintf('Invalid logo path "%s"', $path));
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getResizeWidth(): ?int
    {
        return $this->resizeWidth;
    }
}
