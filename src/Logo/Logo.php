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

    /** @var int */
    private $sourceWidth;

    /** @var int */
    private $sourceHeight;

    public function __construct(string $path, int $resizeToWidth = null, int $resizeToHeight = null)
    {
        $this->path = $path;
        $this->resizeToWidth = $resizeToWidth;
        $this->resizeToHeight = $resizeToHeight;
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

    /** @return mixed */
    public function readImage()
    {
        if (!is_file($this->path)) {
            throw new \Exception(sprintf('Invalid logo path: "%s"', $this->path));
        }

        $contents = file_get_contents($this->path);

        if (!is_string($contents)) {
            throw new \Exception(sprintf('Invalid data at path "%s"', $this->path));
        }

        $image = imagecreatefromstring($contents);

        if (!$image) {
            throw new \Exception(sprintf('Unable to parse image data at path "%s"', $this->path));
        }

        $this->sourceWidth = imagesx($image);
        $this->sourceHeight = imagesy($image);

        return $image;
    }

    public function getTargetWidth(): int
    {
        // Resize width is set: use it
        if (isset($this->resizeToWidth)) {
            return $this->resizeToWidth;
        }

        if (!isset($this->sourceWidth) || !isset($this->sourceHeight)) {
            throw new \Exception('Unable to determine target height: call setResizeToWidth or call readImage first');
        }

        // No resize width or height: use original width
        if (!isset($this->resizeToHeight)) {
            return $this->sourceWidth;
        }

        // Only resize height is set: calculate width
        $ratio = $this->resizeToHeight / $this->sourceHeight;

        return intval($this->sourceWidth * $ratio);
    }

    public function getTargetHeight(): int
    {
        // Resize height is set: use it
        if (isset($this->resizeToHeight)) {
            return $this->resizeToHeight;
        }

        if (!isset($this->sourceWidth) || !isset($this->sourceHeight)) {
            throw new \Exception('Unable to determine target height: call setResizeToHeight or call readImage first');
        }

        // No resize width or height: use original height
        if (!isset($this->resizeToWidth)) {
            return $this->sourceHeight;
        }

        // Only resize width is set: calculate height
        $ratio = $this->resizeToWidth / $this->sourceWidth;

        return intval($this->sourceHeight * $ratio);
    }
}
