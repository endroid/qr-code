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

    /** @var string|null */
    private $imageData;

    /** @var mixed|null */
    private $image;

    /** @var string|null */
    private $mimeType;

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

    public function getImageData(): string
    {
        if (is_string($this->imageData)) {
            return $this->imageData;
        }

        $imageData = file_get_contents($this->path);

        if (!is_string($imageData)) {
            throw new \Exception(sprintf('Invalid data at path "%s"', $this->path));
        }

        $this->imageData = $imageData;

        return $this->imageData;
    }

    public function getImageDataUri(): string
    {
        return 'data:'.$this->getMimeType().';base64,'.base64_encode($this->getImageData());
    }

    public function getImage()
    {
        if ($this->image) {
            return $this->image;
        }

        $imageData = $this->getImageData();
        $image = imagecreatefromstring($imageData);

        if (!$image) {
            throw new \Exception(sprintf('Unable to parse image data at path "%s"', $this->path));
        }

        $this->image = $image;

        return $this->image;
    }

    public function getMimeType(): string
    {
        if (is_string($this->mimeType)) {
            return $this->mimeType;
        }

        if (false !== filter_var($this->path, FILTER_VALIDATE_URL)) {
            $this->mimeType = $this->getMimeTypeFromUrl($this->path);
        }

        $this->mimeType = $this->getMimeTypeFromPath($this->path);

        return $this->mimeType;
    }

    private function getMimeTypeFromUrl(string $url): string
    {
        /** @var mixed $format */
        $format = PHP_VERSION > 80000 ? true : 1;

        $headers = get_headers($url, $format);

        if (!is_array($headers) || !isset($headers['Content-Type'])) {
            throw new \Exception(sprintf('Content type could not be determined for logo URL "%s"', $url));
        }

        return $headers['Content-Type'];
    }

    private function getMimeTypeFromPath(string $path): string
    {
        if (!function_exists('mime_content_type')) {
            throw new \Exception('You need the ext-fileinfo extension to determine logo mime type');
        }

        $mimeType = @mime_content_type($path);

        if (!is_string($mimeType)) {
            throw new \Exception('Could not determine mime type');
        }

        if (!preg_match('#^image/#', $mimeType)) {
            throw new \Exception('Logo path is not an image');
        }

        // Passing mime type image/svg results in invisible images
        if ('image/svg' === $mimeType) {
            return 'image/svg+xml';
        }

        return $mimeType;
    }

    public function getSourceWidth(): int
    {
        $image = $this->getImage();

        return imagesx($image);
    }

    public function getSourceHeight(): int
    {
        $image = $this->getImage();

        return imagesy($image);
    }

    public function getTargetWidth(): int
    {
        // Resize width is set: use it
        if (isset($this->resizeToWidth)) {
            return $this->resizeToWidth;
        }

        // No resize width or height: use original width
        if (!isset($this->resizeToHeight)) {
            return $this->getSourceWidth();
        }

        // Only resize height is set: calculate width
        $ratio = $this->resizeToHeight / $this->getSourceHeight();

        return intval($this->getSourceWidth() * $ratio);
    }

    public function getTargetHeight(): int
    {
        // Resize height is set: use it
        if (isset($this->resizeToHeight)) {
            return $this->resizeToHeight;
        }

        // No resize width or height: use original height
        if (!isset($this->resizeToWidth)) {
            return $this->getSourceHeight();
        }

        // Only resize width is set: calculate height
        $ratio = $this->resizeToWidth / $this->getSourceWidth();

        return intval($this->getSourceHeight() * $ratio);
    }
}
