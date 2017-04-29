<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\Eps;
use BaconQrCode\Writer;

class EpsWriter extends AbstractBaconWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString()
    {
        $renderer = new Eps();
        $renderer->setWidth($this->qrCode->getSize());
        $renderer->setHeight($this->qrCode->getSize());
        $renderer->setMargin(0);
        $renderer->setForegroundColor($this->convertColor($this->qrCode->getForegroundColor()));
        $renderer->setBackgroundColor($this->convertColor($this->qrCode->getBackgroundColor()));

        $writer = new Writer($renderer);
        $string = $writer->writeString(
            $this->qrCode->getText(),
            $this->qrCode->getEncoding(),
            $this->convertErrorCorrectionLevel($this->qrCode->getErrorCorrectionLevel())
        );

        $string = $this->addMargin($string);

        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function addMargin($string)
    {
        $targetSize = $this->qrCode->getSize() + $this->qrCode->getMargin() * 2;

        $lines = explode("\n", $string);

        $sourceBlockSize = 0;
        $additionalWhitespace = $this->qrCode->getSize();
        foreach ($lines as $line) {
            if (preg_match('#[0-9]+ [0-9]+ [0-9]+ [0-9]+ F#i', $line) && strpos($line, $this->qrCode->getSize().' '.$this->qrCode->getSize().' F') === false) {
                $parts = explode(' ', $line);
                $sourceBlockSize = $parts[2];
                $additionalWhitespace = min($additionalWhitespace, $parts[0]);
            }
        }

        $blockCount = ($this->qrCode->getSize() - 2 * $additionalWhitespace) / $sourceBlockSize;
        $targetBlockSize = $this->qrCode->getSize() / $blockCount;

        foreach ($lines as &$line) {
            if (strpos($line, 'BoundingBox') !== false) {
                $line = '%%BoundingBox: 0 0 '.$targetSize.' '.$targetSize;
            } elseif (strpos($line, $this->qrCode->getSize().' '.$this->qrCode->getSize().' F') !== false) {
                $line = '0 0 '.$targetSize.' '.$targetSize.' F';
            } elseif (preg_match('#[0-9]+ [0-9]+ [0-9]+ [0-9]+ F#i', $line)) {
                $parts = explode(' ', $line);
                $parts[0] = $this->qrCode->getMargin() + $targetBlockSize * ($parts[0] - $additionalWhitespace) / $sourceBlockSize;
                $parts[1] = $this->qrCode->getMargin() + $targetBlockSize * ($parts[1] - $sourceBlockSize - $additionalWhitespace) / $sourceBlockSize;
                $parts[2] = $targetBlockSize;
                $parts[3] = $targetBlockSize;
                $line = implode(' ', $parts);
            }
        }

        $string = implode("\n", $lines);

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'image/eps';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedExtensions()
    {
        return ['eps'];
    }
}
