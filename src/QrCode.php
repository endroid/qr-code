<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use BaconQrCode\Common\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\InvalidErrorCorrectionLevelException;
use Endroid\QrCode\Exception\InvalidLabelFontPathException;
use Endroid\QrCode\Exception\MissingWriterException;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\DataUriWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;

class QrCode
{
    const DEFAULT_FONT_PATH = __DIR__ . '/../font/open_sans.ttf';

    /**
     * @var WriterInterface[]
     */
    private $writers;

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
    private $foregroundColor = [
        'r' => 0,
        'g' => 0,
        'b' => 0,
        'a' => 0
    ];

    /**
     * @var array
     */
    private $backgroundColor = [
        'r' => 255,
        'g' => 255,
        'b' => 255,
        'a' => 0
    ];

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
     * @param string $text
     */
    public function __construct($text = '')
    {
        $this->writers = [];
        $this->writersByExtension = [];

        $this->text = $text;

        $this->registerBuiltInWriters();
    }

    protected function registerBuiltInWriters()
    {
        $this->registerWriter(new BinaryWriter($this));
        $this->registerWriter(new DataUriWriter($this));
        $this->registerWriter(new EpsWriter($this));
        $this->registerWriter(new PngWriter($this));
        $this->registerWriter(new SvgWriter($this));
    }

    /**
     * @param WriterInterface $writer
     */
    public function registerWriter(WriterInterface $writer)
    {
        if (!isset($this->writers[get_class($writer)])) {
            $this->writers[get_class($writer)] = $writer;
        }
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
     * @return string
     */
    public function getText()
    {
        return $this->text;
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
     * @return int
     */
    public function getSize()
    {
        return $this->size;
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
     * @return int
     */
    public function getQuietZone()
    {
        return $this->quietZone;
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
     * @return array
     */
    public function getForegroundColor()
    {
        return $this->foregroundColor;
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
     * @return array
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
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
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
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
     * @return int
     */
    public function getErrorCorrectionLevel()
    {
        return $this->errorCorrectionLevel;
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
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $writerClass
     * @return string
     * @throws MissingWriterException
     */
    public function getContentType($writerClass)
    {
        $this->assertValidWriterClass($writerClass);

        return $this->writers[$writerClass]->getContentType();
    }

    /**
     * @param string $writerClass
     * @throws MissingWriterException
     */
    protected function assertValidWriterClass($writerClass)
    {
        if (!isset($this->writers[$writerClass])) {
            throw new MissingWriterException('Invalid writer "'.$writerClass.'"');
        }
    }

    /**
     * @param string $writerClass
     * @return string
     */
    public function writeString($writerClass)
    {
        $this->assertValidWriterClass($writerClass);

        return $this->writers[$writerClass]->writeString();
    }

    /**
     * @param string $path
     * @param string $writerClass
     */
    public function writeFile($path, $writerClass = null)
    {
        $writer = $this->getWriterByPath($path);

        if ($writerClass !== null) {
            $this->assertValidWriterClass($writerClass);
            $writer = $this->writers[$writerClass];
        }

        return $writer->writeFile($path);
    }

    /**
     * @param string $path
     * @return WriterInterface
     */
    public function getWriterByPath($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return $this->getWriterByExtension($extension);
    }

    /**
     * @param string $extension
     * @return WriterInterface
     * @throws MissingWriterException
     */
    public function getWriterByExtension($extension)
    {
        foreach ($this->writers as $writer) {
            if (in_array($extension, $writer->getSupportedExtensions())) {
                return $writer;
            }
        }

        throw new MissingWriterException('Missing writer for extension "'.$extension.'"');
    }
}
