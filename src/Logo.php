<?php

declare(strict_types=1);

namespace Endroid\QrCode;

final class Logo implements LogoInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->validatePath($path);

        $this->path = $path;
    }

    private function validatePath(string $path): void
    {
        if (!file_exists($path)) {
            throw new \Exception(sprintf('Invalid logo path "%s"', $path));
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
