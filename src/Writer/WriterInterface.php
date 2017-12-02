<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;

interface WriterInterface
{
    function writeString(QrCodeInterface $qrCode): string;
    function writeDataUri(QrCodeInterface $qrCode): string;
    function writeFile(QrCodeInterface $qrCode, string $path): string;
    static function getContentType(): string;
    static function supportsExtension(string $extension): bool;
    static function getSupportedExtensions(): array;
    function getName(): string;
}
