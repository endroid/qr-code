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

class EpsWriter extends AbstractWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString()
    {
        $renderer = new Eps();
        $renderer->setWidth($this->qrCode->getSize());
        $renderer->setHeight($this->qrCode->getSize());
        $renderer->setMargin($this->qrCode->getQuietZone());
        $writer = new Writer($renderer);
        $string = $writer->writeString(
            $this->qrCode->getText(),
            $this->qrCode->getEncoding(),
            constant('BaconQrCode\Common\ErrorCorrectionLevel::'.strtoupper($this->qrCode->getErrorCorrectionLevel()))
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
    public function getSupportedExtensions()
    {
        return ['eps'];
    }
}
