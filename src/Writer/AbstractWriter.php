<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;
use ReflectionClass;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function writeDataUri(QrCodeInterface $qrCode)
    {
        $dataUri = 'data:'.$this->getContentType().';base64,'.base64_encode($this->writeString($qrCode));

        return $dataUri;
    }

    /**
     * {@inheritdoc}
     */
    public function writeFile(QrCodeInterface $qrCode, $path)
    {
        $string = $this->writeString($qrCode);
        file_put_contents($path, $string);
    }

    /**
     * {@inheritdoc}
     */
    public static function supportsExtension($extension)
    {
        return in_array($extension, static::getSupportedExtensions());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSupportedExtensions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $reflectionClass = new ReflectionClass($this);
        $className = $reflectionClass->getShortName();
        $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Writer', '', $className)));

        return $name;
    }
}
