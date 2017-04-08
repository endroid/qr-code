<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Renderer\Image\Svg;
use BaconQrCode\Writer;
use Endroid\QrCode\Exception\InvalidErrorCorrectionLevelException;
use Endroid\QrCode\Exception\InvalidLabelFontPathException;

class QrCode
{
    const DEFAULT_FONT_PATH = __DIR__.'/../font/open_sans.ttf';

    /**
     * @var string
     */
    private $text;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $quietZone;

    /**
     * @var array
     */
    private $foregroundColor = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];

    /**
     * @var array
     */
    private $backgroundColor = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0];

    /**
     * @var string
     */
    private $encoding = 'UTF-8';

    /**
     * @var int
     */
    private $errorCorrectionLevel = ErrorCorrectionLevel::L;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $labelFontSize;

    /**
     * @var string
     */
    private $labelFontPath;

    /**
     * QrCode constructor.
     * @param string $text
     * @param int $size
     * @param int $quietZone
     */
    public function __construct($text = '', $size = 200, $quietZone = 2)
    {
        $this->text = $text;
        $this->size = $size;
        $this->quietZone = $quietZone;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @param int $quietZone
     * @return $this
     */
    public function setQuietZone($quietZone)
    {
        $this->quietZone = $quietZone;

        return $this;
    }

    /**
     * @param array $foregroundColor
     * @return $this
     */
    public function setForegroundColor($foregroundColor)
    {
        if (!isset($foregroundColor['a'])) {
            $foregroundColor['a'] = 0;
        }

        $this->foregroundColor = $foregroundColor;

        return $this;
    }

    /**
     * @param array $backgroundColor
     * @return $this
     */
    public function setBackgroundColor($backgroundColor)
    {
        if (!isset($backgroundColor['a'])) {
            $backgroundColor['a'] = 0;
        }

        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @param int $errorCorrectionLevel
     * @throws InvalidErrorCorrectionLevelException
     */
    public function setErrorCorrectionLevel($errorCorrectionLevel)
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;
    }

    /**
     * @param string $label
     * @param int $labelFontSize
     * @param string $labelFontPath
     * @throws InvalidLabelFontPathException
     */
    public function setLabel($label, $labelFontSize = 16, $labelFontPath = self::DEFAULT_FONT_PATH)
    {
        $labelFontPath = realpath($labelFontPath);

        if (!file_exists($labelFontPath)) {
            throw new InvalidLabelFontPathException('Invalid label font path: '.$labelFontPath);
        }

        $this->label = $label;
        $this->labelFontSize = $labelFontSize;
        $this->labelFontPath = $labelFontPath;
    }

    /**
     * @return string
     */
    public function getPngData()
    {
        $renderer = new Png();
        $renderer->setWidth($this->size);
        $renderer->setHeight($this->size);
        $renderer->setMargin($this->quietZone);
        $writer = new Writer($renderer);
        $pngData = $writer->writeString($this->text, $this->encoding, $this->errorCorrectionLevel);

        return $pngData;
    }

    /**
     * @return string
     */
    public function getSvgData()
    {
        $renderer = new Svg();
        $renderer->setWidth($this->size);
        $renderer->setHeight($this->size);
        $renderer->setMargin($this->quietZone);
        $writer = new Writer($renderer);
        $svgData = $writer->writeString($this->text, $this->encoding, $this->errorCorrectionLevel);

        return $svgData;
    }

    /**
     * @return string
     */
    public function getPngDataUri()
    {
        $pngData = $this->getPngData();
        $pngDataUri = 'data:image/png;base64,'.base64_encode($pngData);

        return $pngDataUri;
    }
}