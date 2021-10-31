<?php

declare(strict_types=1);

namespace Endroid\QrCode\ImageData;

use Endroid\QrCode\Logo\LogoInterface;

class LogoImageData
{
    private string $data;

    /** @var mixed */
    private $image;

    private string $mimeType;
    private int $width;
    private int $height;
    private bool $punchoutBackground;

    /** @param mixed $image */
    private function __construct(
        string $data,
        $image,
        string $mimeType,
        int $width,
        int $height,
        bool $punchoutBackground
    ) {
        $this->data = $data;
        $this->image = $image;
        $this->mimeType = $mimeType;
        $this->width = $width;
        $this->height = $height;
        $this->punchoutBackground = $punchoutBackground;
    }

    public static function createForLogo(LogoInterface $logo): self
    {
        $data = @file_get_contents($logo->getPath());

        if (!is_string($data)) {
            throw new \Exception(sprintf('Invalid data at path "%s"', $logo->getPath()));
        }

        if (false !== filter_var($logo->getPath(), FILTER_VALIDATE_URL)) {
            $mimeType = self::detectMimeTypeFromUrl($logo->getPath());
        } else {
            $mimeType = self::detectMimeTypeFromPath($logo->getPath());
        }

        $width = $logo->getResizeToWidth();
        $height = $logo->getResizeToHeight();

        if ('image/svg+xml' === $mimeType) {
            if (null === $width || null === $height) {
                throw new \Exception('SVG Logos require an explicitly set resize width and height');
            }

            return new self($data, null, $mimeType, $width, $height, $logo->getPunchoutBackground());
        }

        $image = imagecreatefromstring($data);

        if (!$image) {
            throw new \Exception(sprintf('Unable to parse image data at path "%s"', $logo->getPath()));
        }

        // No target width and height specified: use from original image
        if (null !== $width && null !== $height) {
            return new self($data, $image, $mimeType, $width, $height, $logo->getPunchoutBackground());
        }

        // Only target width specified: calculate height
        if (null !== $width && null === $height) {
            return new self($data, $image, $mimeType, $width, intval(imagesy($image) * $width / imagesx($image)), $logo->getPunchoutBackground());
        }

        // Only target height specified: calculate width
        if (null === $width && null !== $height) {
            return new self($data, $image, $mimeType, intval(imagesx($image) * $height / imagesy($image)), $height, $logo->getPunchoutBackground());
        }

        return new self($data, $image, $mimeType, imagesx($image), imagesy($image), $logo->getPunchoutBackground());
    }

    public function getData(): string
    {
        return $this->data;
    }

    /** @return mixed */
    public function getImage()
    {
        return $this->image;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getPunchoutBackground(): bool
    {
        return $this->punchoutBackground;
    }

    public function createDataUri(): string
    {
        return 'data:'.$this->mimeType.';base64,'.base64_encode($this->data);
    }

    private static function detectMimeTypeFromUrl(string $url): string
    {
        /** @var mixed $format */
        $format = PHP_VERSION_ID >= 80000 ? true : 1;

        $headers = get_headers($url, $format);

        if (!is_array($headers) || !isset($headers['Content-Type'])) {
            throw new \Exception(sprintf('Content type could not be determined for logo URL "%s"', $url));
        }

        return is_array($headers['Content-Type']) ? $headers['Content-Type'][1] : $headers['Content-Type'];
    }

    private static function detectMimeTypeFromPath(string $path): string
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
}
