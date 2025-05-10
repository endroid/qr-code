<?php

declare(strict_types=1);

namespace Endroid\QrCode\ImageData;

use Endroid\QrCode\Logo\LogoInterface;

final readonly class LogoImageData
{
    private function __construct(
        private string $data,
        private ?\GdImage $image,
        private string $mimeType,
        private int $width,
        private int $height,
        private bool $punchoutBackground,
    ) {
    }

    public static function createForLogo(LogoInterface $logo): self
    {
        error_clear_last();
        $data = @file_get_contents($logo->getPath());

        if (!is_string($data)) {
            $errorDetails = error_get_last()['message'] ?? 'invalid data';
            throw new \Exception(sprintf('Could not read logo image data from path "%s": %s', $logo->getPath(), $errorDetails));
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

        if (!function_exists('imagecreatefromstring')) {
            throw new \Exception('Function "imagecreatefromstring" does not exist: check your GD installation');
        }

        error_clear_last();
        $image = @imagecreatefromstring($data);

        if (!$image) {
            $errorDetails = error_get_last()['message'] ?? 'invalid data';
            throw new \Exception(sprintf('Unable to parse image data at path "%s": %s', $logo->getPath(), $errorDetails));
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

    public function getImage(): \GdImage
    {
        if (!$this->image instanceof \GdImage) {
            throw new \Exception('SVG Images have no image resource');
        }

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
        $headers = get_headers($url, true);

        if (!is_array($headers)) {
            throw new \Exception(sprintf('Could not retrieve headers to determine content type for logo URL "%s"', $url));
        }

        $headers = array_combine(array_map('strtolower', array_keys($headers)), $headers);

        if (!isset($headers['content-type'])) {
            throw new \Exception(sprintf('Content type could not be determined for logo URL "%s"', $url));
        }

        return is_array($headers['content-type']) ? $headers['content-type'][1] : $headers['content-type'];
    }

    private static function detectMimeTypeFromPath(string $path): string
    {
        if (!function_exists('mime_content_type')) {
            throw new \Exception('You need the ext-fileinfo extension to determine logo mime type');
        }

        error_clear_last();
        $mimeType = @mime_content_type($path);

        if (!is_string($mimeType)) {
            $errorDetails = error_get_last()['message'] ?? 'invalid data';
            throw new \Exception(sprintf('Could not determine mime type: %s', $errorDetails));
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
