<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

final class Logo implements LogoInterface
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function create(string $path): self
    {
        return new self($path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
}
