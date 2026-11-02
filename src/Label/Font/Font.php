<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label\Font;

final readonly class Font implements FontInterface
{
    public function __construct(
        private string $path,
        private int $size = 16,
    ) {
        $this->assertValidPath($path);
    }

    private function assertValidPath(string $path): void
    {
        if (!file_exists($path)) {
            throw new \Exception(sprintf('Invalid font path "%s"', $path));
        }
    }

    #[\Override]
    public function getPath(): string
    {
        return $this->path;
    }

    #[\Override]
    public function getSize(): int
    {
        return $this->size;
    }
}
