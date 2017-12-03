<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use BaconQrCode\Renderer\Image\Eps;
use BaconQrCode\Writer;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Traits\BaconConversionTrait;

class EpsWriter extends AbstractWriter
{
    use BaconConversionTrait;

    public function writeString(QrCodeInterface $qrCode): string
    {
        $renderer = new Eps();
        $renderer->setWidth($qrCode->getSize());
        $renderer->setHeight($qrCode->getSize());
        $renderer->setMargin(0);
        $renderer->setForegroundColor($this->convertColor($qrCode->getForegroundColor()));
        $renderer->setBackgroundColor($this->convertColor($qrCode->getBackgroundColor()));

        $writer = new Writer($renderer);
        $string = $writer->writeString($qrCode->getText(), $qrCode->getEncoding(), $this->convertErrorCorrectionLevel($qrCode->getErrorCorrectionLevel()));
        $string = $this->addMargin($string, $qrCode);

        return $string;
    }

    private function addMargin(string $string, QrCodeInterface $qrCode): string
    {
        $targetSize = $qrCode->getSize() + $qrCode->getMargin() * 2;

        $lines = explode("\n", $string);

        $sourceBlockSize = 0;
        $additionalWhitespace = $qrCode->getSize();
        foreach ($lines as $line) {
            if (preg_match('#[0-9]+ [0-9]+ [0-9]+ [0-9]+ F#i', $line) && false === strpos($line, $qrCode->getSize().' '.$qrCode->getSize().' F')) {
                $parts = explode(' ', $line);
                $sourceBlockSize = $parts[2];
                $additionalWhitespace = min($additionalWhitespace, $parts[0]);
            }
        }

        $blockCount = ($qrCode->getSize() - 2 * $additionalWhitespace) / $sourceBlockSize;
        $targetBlockSize = $qrCode->getSize() / $blockCount;

        foreach ($lines as &$line) {
            if (false !== strpos($line, 'BoundingBox')) {
                $line = '%%BoundingBox: 0 0 '.$targetSize.' '.$targetSize;
            } elseif (false !== strpos($line, $qrCode->getSize().' '.$qrCode->getSize().' F')) {
                $line = '0 0 '.$targetSize.' '.$targetSize.' F';
            } elseif (preg_match('#[0-9]+ [0-9]+ [0-9]+ [0-9]+ F#i', $line)) {
                $parts = explode(' ', $line);
                $parts[0] = $qrCode->getMargin() + $targetBlockSize * ($parts[0] - $additionalWhitespace) / $sourceBlockSize;
                $parts[1] = $qrCode->getMargin() + $targetBlockSize * ($parts[1] - $additionalWhitespace) / $sourceBlockSize;
                $parts[2] = $targetBlockSize;
                $parts[3] = $targetBlockSize;
                $line = implode(' ', $parts);
            }
        }

        $string = implode("\n", $lines);

        return $string;
    }

    public static function getContentType(): string
    {
        return 'image/eps';
    }

    public static function getSupportedExtensions(): array
    {
        return ['eps'];
    }

    public function getName(): string
    {
        return 'eps';
    }
}
