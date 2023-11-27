<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;

final class Logo implements LogoInterface
{
    public function __construct(
        private string $path,
        private int|null $resizeToWidth = null,
        private int|null $resizeToHeight = null,
        private bool $punchoutBackground = false,
        private int $margin = 0,
        private ColorInterface $backgroundColor = new Color(255, 255, 255, 127)
    ) {
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

    public function getResizeToWidth(): int|null
    {
        return $this->resizeToWidth;
    }

    public function setResizeToWidth(int|null $resizeToWidth): self
    {
        $this->resizeToWidth = $resizeToWidth;

        return $this;
    }

    public function getResizeToHeight(): int|null
    {
        return $this->resizeToHeight;
    }

    public function setResizeToHeight(int|null $resizeToHeight): self
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

    public function getMargin(): int
    {
        return $this->margin;
    }

    public function setMargin(int $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    public function getBackgroundColor(): ColorInterface
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(ColorInterface $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }
}
