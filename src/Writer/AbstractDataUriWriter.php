<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

abstract class AbstractDataUriWriter extends AbstractWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString()
    {
        $string = $this->qrCode->writeString($this->getInternalWriterClass());
        $string = 'data:'.$this->qrCode->getContentType($this->getInternalWriterClass()).';base64,'.base64_encode($string);

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
    protected function getSupportedExtensions()
    {
        return [];
    }

    /**
     * @return WriterInterface
     */
    abstract public function getInternalWriterClass();
}
