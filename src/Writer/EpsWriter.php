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
