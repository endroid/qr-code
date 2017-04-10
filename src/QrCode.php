<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\Eps;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Renderer\Image\Svg;
use BaconQrCode\Writer;
use Endroid\QrCode\Exception\InvalidErrorCorrectionLevelException;
use Endroid\QrCode\Exception\InvalidTypeException;
use Endroid\QrCode\Exception\InvalidLabelFontPathException;

class QrCode
{
    const DEFAULT_FONT_PATH = __DIR__ . '/../font/open_sans.ttf';

    const TYPE_BINARY = 'binary';
    const TYPE_DATA_URI = 'data_uri';
    const TYPE_EPS = 'eps';
    const TYPE_PNG = 'png';
    const TYPE_SVG = 'svg';

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
     * @var array
     */
    private static $types = [
        self::TYPE_BINARY,
        self::TYPE_DATA_URI,
        self::TYPE_EPS,
        self::TYPE_PNG,
        self::TYPE_SVG,
    ];

    /**
     * @var array
     */
    private static $contentTypes = [
        self::TYPE_BINARY => 'text/plain',
        self::TYPE_DATA_URI => 'text/plain',
        self::TYPE_EPS => 'image/eps',
        self::TYPE_PNG => 'image/png',
        self::TYPE_SVG => 'image/svg+xml'
    ];

    /**
     * @var array
     */
    private static $extensions = [
        'txt' => self::TYPE_BINARY,
        'bin' => self::TYPE_BINARY,
        'eps' => self::TYPE_EPS,
        'png' => self::TYPE_PNG,
        'svg' => self::TYPE_SVG,
    ];

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
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @param int $errorCorrectionLevel
     * @return $this
     * @throws InvalidErrorCorrectionLevelException
     */
    public function setErrorCorrectionLevel($errorCorrectionLevel)
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
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
            throw new InvalidLabelFontPathException('Invalid label font path: ' . $labelFontPath);
        }

        $this->label = $label;
        $this->labelFontSize = $labelFontSize;
        $this->labelFontPath = $labelFontPath;
    }

    /**
     * @param string $typeOrPath
     * @return string
     */
    public function write($typeOrPath)
    {
        $type = $typeOrPath;
        $path = null;

        if (!$this->isValidType($type)) {
            $path = $typeOrPath;
            $type = $this->getTypeByPath($path);
        }

        return $this->{'write'.str_replace('_', '', ucwords($type, '_'))}($path);
    }

    /**
     * @param string $type
     * @return string
     * @throws InvalidTypeException
     */
    public function getContentType($type)
    {
        if (!$this->isValidType($type)) {
            throw new InvalidTypeException('Invalid type "'.$type.'"');
        }

        return self::$contentTypes[$type];
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function isValidType($type)
    {
        return in_array($type, self::$types);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function getTypeByPath($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return self::getTypeByExtension($extension);
    }

    /**
     * @param string $extension
     * @return string
     * @throws InvalidTypeException
     */
    public static function getTypeByExtension($extension)
    {
        if (isset(self::$extensions[$extension])) {
            return self::$extensions[$extension];
        }

        throw new InvalidTypeException('Incompatible extension "'.$extension.'"');
    }

    /**
     * @param string|null $path
     * @return string
     */
    public function writeBinary($path = null)
    {
        $data = '0001010101'; // Not implemented yet

        if (!is_null($path)) {
            file_put_contents($path, $data);
        }

        return $data;
    }

    /**
     * @param string|null $path
     * @return string
     */
    public function writeEps($path = null)
    {
        $renderer = new Eps();
        $renderer->setWidth($this->size);
        $renderer->setHeight($this->size);
        $renderer->setMargin($this->quietZone);
        $writer = new Writer($renderer);
        $data = $writer->writeString($this->text, $this->encoding, $this->errorCorrectionLevel);

        if (!is_null($path)) {
            file_put_contents($path, $data);
        }

        return $data;
    }

    /**
     * @param string|null $path
     * @return string
     */
    public function writePng($path = null)
    {
        $renderer = new Png();
        $renderer->setWidth($this->size);
        $renderer->setHeight($this->size);
        $renderer->setMargin($this->quietZone);
        $writer = new Writer($renderer);
        $data = $writer->writeString($this->text, $this->encoding, $this->errorCorrectionLevel);

        if (!is_null($path)) {
            file_put_contents($path, $data);
        }

        return $data;
    }

    /**
     * @param string|null $path
     * @return string
     */
    public function writeSvg($path = null)
    {
        $renderer = new Svg();
        $renderer->setWidth($this->size);
        $renderer->setHeight($this->size);
        $renderer->setMargin($this->quietZone);
        $writer = new Writer($renderer);
        $data = $writer->writeString($this->text, $this->encoding, $this->errorCorrectionLevel);

        if (!is_null($path)) {
            file_put_contents($path, $data);
        }

        return $data;
    }

    /**
     * @param string|null $path
     * @return string
     */
    public function writeUri($path = null)
    {
        $data = $this->writePng();
        $data = 'data:image/png;base64,'.base64_encode($data);

        if (!is_null($path)) {
            file_put_contents($path, $data);
        }

        return $data;
    }
}