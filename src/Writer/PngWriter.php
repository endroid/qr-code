<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;

class PngWriter extends AbstractWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString()
    {
        $renderer = new Png();
        $renderer->setWidth($this->qrCode->getSize());
        $renderer->setHeight($this->qrCode->getSize());
        $renderer->setMargin($this->qrCode->getQuietZone());
        $writer = new Writer($renderer);
        $string = $writer->writeString(
            $this->qrCode->getText(),
            $this->qrCode->getEncoding(),
            $this->qrCode->getErrorCorrectionLevel()
        );

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'image/png';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return ['png'];
    }
}
