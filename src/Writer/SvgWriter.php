<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use BaconQrCode\Renderer\Image\Svg;
use BaconQrCode\Writer;
use SimpleXMLElement;

class SvgWriter extends AbstractBaconWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString()
    {
        $renderer = new Svg();
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

        $xml = new SimpleXMLElement($string);
        $xml['width'] = $targetSize;
        $xml['height'] = $targetSize;
        $xml['viewBox'] = '0 0 '.$targetSize.' '.$targetSize;
        $xml->rect['width'] = $targetSize;
        $xml->rect['height'] = $targetSize;

        $additionalWhitespace = $targetSize;
        foreach ($xml->use as $block) {
            $additionalWhitespace = min($additionalWhitespace, (int) $block['x']);
        }

        $sourceBlockSize = (int) $xml->defs->rect['width'];
        $blockCount = ($this->qrCode->getSize() - 2 * $additionalWhitespace) / $sourceBlockSize;
        $targetBlockSize = $this->qrCode->getSize() / $blockCount;

        $xml->defs->rect['width'] = $targetBlockSize;
        $xml->defs->rect['height'] = $targetBlockSize;

        foreach ($xml->use as $block) {
            $block['x'] = $this->qrCode->getMargin() + $targetBlockSize * ($block['x'] - $additionalWhitespace) / $sourceBlockSize;
            $block['y'] = $this->qrCode->getMargin() + $targetBlockSize * ($block['y'] - $additionalWhitespace) / $sourceBlockSize;
        }

        return $xml->asXML();
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'image/svg+xml';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedExtensions()
    {
        return ['svg'];
    }
}
