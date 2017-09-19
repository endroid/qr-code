<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;

interface WriterInterface
{
    /**
     * @param QrCodeInterface $qrCode
     *
     * @return string
     */
    public function writeString(QrCodeInterface $qrCode);

    /**
     * @param QrCodeInterface $qrCode
     *
     * @return string
     */
    public function writeDataUri(QrCodeInterface $qrCode);

    /**
     * @param QrCodeInterface $qrCode
     * @param string          $path
     */
    public function writeFile(QrCodeInterface $qrCode, $path);

    /**
     * @return string
     */
    public static function getContentType();

    /**
     * @param string $extension
     *
     * @return bool
     */
    public static function supportsExtension($extension);

    /**
     * @return string[]
     */
    public static function getSupportedExtensions();

    /**
     * @return string
     */
    public function getName();
}
