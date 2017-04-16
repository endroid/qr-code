<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCode;

interface WriterInterface
{
    /**
     * @param QrCode $qrCode
     */
    public function __construct(QrCode $qrCode);

    /**
     * @return string
     */
    public function writeString();

    /**
     * @param string $path
     */
    public function writeFile($path);

    /**
     * @return string
     */
    public function getContentType();

    /**
     * @return array
     */
    public function getSupportedExtensions();
}
