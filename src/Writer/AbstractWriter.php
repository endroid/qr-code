<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCode;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * @var QrCode
     */
    protected $qrCode;

    /**
     * {@inheritdoc}
     */
    public function __construct(QrCode $qrCode)
    {
        $this->qrCode = $qrCode;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function writeString();

    /**
     * {@inheritdoc}
     */
    public function writeFile($path)
    {
        $string = $this->writeString();
        file_put_contents($path, $string);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getContentType();

    /**
     * @param string $extension
     * @return bool
     */
    public function supportsExtension($extension)
    {
        return in_array($extension, $this->getSupportedExtensions());
    }

    /**
     * @return array
     */
    protected function getSupportedExtensions()
    {
        return [];
    }
}
