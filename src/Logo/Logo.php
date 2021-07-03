<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

final class Logo implements LogoInterface
{
    /** @var string */
    private $path;

    /** @var int|null */
    private $resizeToWidth;

    /** @var int|null */
    private $resizeToHeight;

    /** @var bool */
    private $punchoutBackground;

    public function __construct(string $path, ?int $resizeToWidth = null, ?int $resizeToHeight = null, bool $punchoutBackground = false)
    {
        $this->path = $path;
        $this->resizeToWidth = $resizeToWidth;
        $this->resizeToHeight = $resizeToHeight;
        $this->punchoutBackground = $punchoutBackground;
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

    public function getResizeToWidth(): ?int
    {
        return $this->resizeToWidth;
    }

    public function setResizeToWidth(?int $resizeToWidth): self
    {
        $this->resizeToWidth = $resizeToWidth;

        return $this;
    }

    public function getResizeToHeight(): ?int
    {
        return $this->resizeToHeight;
    }

    public function setResizeToHeight(?int $resizeToHeight): self
    {
        $this->resizeToHeight = $resizeToHeight;

        return $this;
    }

    public function getPunchoutBackground(): bool
    {
        return $this->punchoutBackground;
    }

    public function setPunchoutBackground(bool $punchoutBackground): self
    {
        $this->punchoutBackground = $punchoutBackground;

        return $this;
    }
}
