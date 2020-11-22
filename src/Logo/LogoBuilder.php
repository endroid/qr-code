<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

final class LogoBuilder implements LogoBuilderInterface
{
    private string $path;
    private ?int $resizeWidth = null;

    public static function create(): self
    {
        return new static();
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function resizeWidth(int $resizeWidth): self
    {
        $this->resizeWidth = $resizeWidth;

        return $this;
    }

    public function build(): Logo
    {
        return new Logo($this->path, $this->resizeWidth);
    }
}
