<?php

declare(strict_types=1);

namespace Endroid\QrCode\ImageData;

use Endroid\QrCode\Logo\LogoInterface;

class LogoImageData
{
    /** @var string */
    private $data;

    /** @var mixed */
    private $image;

    /** @var string */
    private $mimeType;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /** @param mixed $image */
    private function __construct(
        string $data,
        $image,
        string $mimeType,
        int $width,
        int $height
    ) {
        $this->data = $data;
        $this->image = $image;
        $this->mimeType = $mimeType;
        $this->width = $width;
        $this->height = $height;
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

        $image = imagecreatefromstring($data);

        if (!$image) {
            throw new \Exception(sprintf('Unable to parse image data at path "%s"', $logo->getPath()));
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $resizeToWidth = $logo->getResizeToWidth();
        $resizeToHeight = $logo->getResizeToHeight();

        // Fixed resize width and height
        if (is_int($resizeToWidth) && is_int($resizeToHeight)) {
            $width = $resizeToWidth;
            $height = $resizeToHeight;
        }

        // Only fixed width: calculate height
        if (is_int($resizeToWidth) && is_null($resizeToHeight)) {
            $width = $resizeToWidth;
            $height = intval(imagesy($image) * $resizeToWidth / imagesx($image));
        }

        // Only fixed height: calculate width
        if (is_int($resizeToHeight) && is_null($resizeToWidth)) {
            $height = $resizeToHeight;
            $width = intval(imagesx($image) * $resizeToHeight / imagesy($image));
        }

        return new self($data, $image, $mimeType, $width, $height);
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

    public function createDataUri(): string
    {
        return 'data:'.$this->mimeType.';base64,'.base64_encode($this->data);
    }

    private static function detectMimeTypeFromUrl(string $url): string
    {
        /** @var mixed $format */
        $format = PHP_VERSION > 80000 ? true : 1;

        $headers = get_headers($url, $format);

        if (!is_array($headers) || !isset($headers['Content-Type'])) {
            throw new \Exception(sprintf('Content type could not be determined for logo URL "%s"', $url));
        }

        return $headers['Content-Type'];
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
