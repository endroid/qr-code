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
use Endroid\QrCode\QrCodeInterface;
use SimpleXMLElement;

class SvgWriter extends AbstractBaconWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString(QrCodeInterface $qrCode)
    {
        $renderer = new Svg();
        $renderer->setWidth($qrCode->getSize());
        $renderer->setHeight($qrCode->getSize());
        $renderer->setMargin(0);
        $renderer->setForegroundColor($this->convertColor($qrCode->getForegroundColor()));
        $renderer->setBackgroundColor($this->convertColor($qrCode->getBackgroundColor()));

        $writer = new Writer($renderer);
        $string = $writer->writeString($qrCode->getText(), $qrCode->getEncoding(), $this->convertErrorCorrectionLevel($qrCode->getErrorCorrectionLevel()));

        $string = $this->addMargin($string, $qrCode->getMargin(), $qrCode->getSize());

        return $string;
    }

    /**
     * @param string $string
     * @param int $margin
     * @param int $size
     * @return string
     */
    protected function addMargin($string, $margin, $size)
    {
        $targetSize = $size + $margin * 2;

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
        $blockCount = ($size - 2 * $additionalWhitespace) / $sourceBlockSize;
        $targetBlockSize = $size / $blockCount;

        $xml->defs->rect['width'] = $targetBlockSize;
        $xml->defs->rect['height'] = $targetBlockSize;

        foreach ($xml->use as $block) {
            $block['x'] = $margin + $targetBlockSize * ($block['x'] - $additionalWhitespace) / $sourceBlockSize;
            $block['y'] = $margin + $targetBlockSize * ($block['y'] - $additionalWhitespace) / $sourceBlockSize;
        }

        return $xml->asXML();
    }

    /**
     * {@inheritdoc}
     */
    public static function getContentType()
    {
        return 'image/svg+xml';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSupportedExtensions()
    {
        return ['svg'];
    }
}
