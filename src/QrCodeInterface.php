<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

interface QrCodeInterface
{
    /**
     * @return string
     */
    public function getText();

    /**
     * @return int
     */
    public function getSize();

    /**
     * @return int
     */
    public function getMargin();

    /**
     * @return int[]
     */
    public function getForegroundColor();

    /**
     * @return int[]
     */
    public function getBackgroundColor();

    /**
     * @return string
     */
    public function getEncoding();

    /**
     * @return string
     */
    public function getErrorCorrectionLevel();

    /**
     * @return string
     */
    public function getLogoPath();

    /**
     * @return int
     */
    public function getLogoWidth();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getLabelFontPath();

    /**
     * @return int
     */
    public function getLabelFontSize();

    /**
     * @return string
     */
    public function getLabelAlignment();

    /**
     * @return int[]
     */
    public function getLabelMargin();

    /**
     * @return bool
     */
    public function getValidateResult();

    /**
     * @param WriterRegistryInterface $writerRegistry
     * @return mixed
     */
    public function setWriterRegistry(WriterRegistryInterface $writerRegistry);
}
