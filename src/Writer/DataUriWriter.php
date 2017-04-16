<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

class DataUriWriter extends PngWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString()
    {
        $string = parent::writeString();
        $string = 'data:image/png;base64,'.base64_encode($string);

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'text/plain';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return [];
    }
}
