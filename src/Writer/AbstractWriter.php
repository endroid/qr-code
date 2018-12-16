<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use BaconQrCode\Encoder\Encoder;
use Endroid\QrCode\QrCodeInterface;

abstract class AbstractWriter implements WriterInterface
{
    public function writeDataUri(QrCodeInterface $qrCode): string
    {
        $dataUri = 'data:'.$this->getContentType().';base64,'.base64_encode($this->writeString($qrCode));

        return $dataUri;
    }

    public function writeFile(QrCodeInterface $qrCode, string $path): void
    {
        $string = $this->writeString($qrCode);
        file_put_contents($path, $string);
    }

    public static function supportsExtension(string $extension): bool
    {
        return in_array($extension, static::getSupportedExtensions());
    }

    public static function getSupportedExtensions(): array
    {
        return [];
    }

    abstract public function getName(): string;
}
