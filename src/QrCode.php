<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use Endroid\QrCode\Exception\InvalidPathException;
use Endroid\QrCode\Exception\InvalidWriterException;
use Endroid\QrCode\Exception\UnsupportedExtensionException;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\PngDataUriWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgDataUriWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;

class QrCode
{
    const LABEL_FONT_PATH_DEFAULT = __DIR__ . '/../assets/noto_sans.otf';

    /**
     * @var WriterInterface[]
     */
    protected $writers;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $size = 200;

    /**
     * @var int
     */
    protected $margin = 10;

    /**
     * @var array
     */
    protected $foregroundColor = [
        'r' => 0,
        'g' => 0,
        'b' => 0
    ];

    /**
     * @var array
     */
    protected $backgroundColor = [
        'r' => 255,
        'g' => 255,
        'b' => 255
    ];

    /**
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * @var ErrorCorrectionLevel
     */
    protected $errorCorrectionLevel;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var int
     */
    protected $labelFontSize = 16;

    /**
     * @var string
     */
    protected $labelFontPath = self::LABEL_FONT_PATH_DEFAULT;

    /**
     * @var LabelAlignment
     */
    protected $labelAlignment;

    /**
     * @var array
     */
    protected $labelMargin = [
        't' => 0,
        'r' => 0,
        'b' => 10,
        'l' => 0,
    ];

    /**
     * @var string
     */
    protected $logoPath;

    /**
     * @var int
     */
    protected $logoSize;

    /**
     * @var bool
     */
    protected $validateResult = false;

    /**
     * @param string $text
     */
    public function __construct($text = '')
    {
        $this->writers = [];
        $this->writersByExtension = [];

        $this->text = $text;

        $this->errorCorrectionLevel = new ErrorCorrectionLevel(ErrorCorrectionLevel::LOW);
        $this->labelAlignment = new LabelAlignment(LabelAlignment::CENTER);

        $this->registerBuiltInWriters();
    }

    protected function registerBuiltInWriters()
    {
        $this->registerWriter(new BinaryWriter($this));
        $this->registerWriter(new EpsWriter($this));
        $this->registerWriter(new PngDataUriWriter($this));
        $this->registerWriter(new PngWriter($this));
        $this->registerWriter(new SvgDataUriWriter($this));
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
     * @return WriterInterface[]
     */
    public function getRegisteredWriters()
    {
        return $this->writers;
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
     * @param int $margin
     * @return $this
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;

        return $this;
    }

    /**
     * @return int
     */
    public function getMargin()
    {
        return $this->margin;
    }

    /**
     * @param array $foregroundColor
     * @return $this
     */
    public function setForegroundColor($foregroundColor)
    {
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
     * @param string $errorCorrectionLevel
     * @return $this
     */
    public function setErrorCorrectionLevel($errorCorrectionLevel)
    {
        $this->errorCorrectionLevel = new ErrorCorrectionLevel($errorCorrectionLevel);

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCorrectionLevel()
    {
        return $this->errorCorrectionLevel->getValue();
    }

    /**
     * @param string $label
     * @param int $labelFontSize
     * @param string $labelFontPath
     * @param string $labelAlignment
     * @param array $labelMargin
     * @return $this
     */
    public function setLabel($label, $labelFontSize = null, $labelFontPath = null, $labelAlignment = null, $labelMargin = null)
    {
        $this->label = $label;

        if ($labelFontSize !== null) {
            $this->setLabelFontSize($labelFontSize);
        }

        if ($labelFontPath !== null) {
            $this->setLabelFontPath($labelFontPath);
        }

        if ($labelAlignment !== null) {
            $this->setLabelAlignment($labelAlignment);
        }

        if ($labelMargin !== null) {
            $this->setLabelMargin($labelMargin);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param int $labelFontSize
     * @return $this
     */
    public function setLabelFontSize($labelFontSize)
    {
        $this->labelFontSize = $labelFontSize;

        return $this;
    }

    /**
     * @return int
     */
    public function getLabelFontSize()
    {
        return $this->labelFontSize;
    }

    /**
     * @param string $labelFontPath
     * @return $this
     * @throws InvalidPathException
     */
    public function setLabelFontPath($labelFontPath)
    {
        $labelFontPath = realpath($labelFontPath);

        if (!is_file($labelFontPath)) {
            throw new InvalidPathException('Invalid label font path: ' . $labelFontPath);
        }

        $this->labelFontPath = $labelFontPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelFontPath()
    {
        return $this->labelFontPath;
    }

    /**
     * @param string $labelAlignment
     * @return $this
     */
    public function setLabelAlignment($labelAlignment)
    {
        $this->labelAlignment = new LabelAlignment($labelAlignment);

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelAlignment()
    {
        return $this->labelAlignment->getValue();
    }

    /**
     * @param array $labelMargin
     * @return $this
     */
    public function setLabelMargin(array $labelMargin)
    {
        $this->labelMargin = array_merge($this->labelMargin, $labelMargin);

        return $this;
    }

    /**
     * @return array
     */
    public function getLabelMargin()
    {
        return $this->labelMargin;
    }

    /**
     * @param string $logoPath
     * @return $this
     * @throws InvalidPathException
     */
    public function setLogoPath($logoPath)
    {
        $logoPath = realpath($logoPath);

        if (!is_file($logoPath)) {
            throw new InvalidPathException('Invalid logo path: ' . $logoPath);
        }

        $this->logoPath = $logoPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogoPath()
    {
        return $this->logoPath;
    }

    /**
     * @param int $logoSize
     * @return $this
     */
    public function setLogoSize($logoSize)
    {
        $this->logoSize = $logoSize;

        return $this;
    }

    /**
     * @return int
     */
    public function getLogoSize()
    {
        return $this->logoSize;
    }

    /**
     * @param bool $validateResult
     * @return $this
     */
    public function setValidateResult($validateResult)
    {
        $this->validateResult = $validateResult;

        return $this;
    }

    /**
     * @return bool
     */
    public function getValidateResult()
    {
        return $this->validateResult;
    }

    /**
     * @param string $writerClass
     * @return string
     * @throws InvalidWriterException
     */
    public function getContentType($writerClass)
    {
        $this->assertValidWriterClass($writerClass);

        return $this->writers[$writerClass]->getContentType();
    }

    /**
     * @param string $writerClass
     * @throws InvalidWriterException
     */
    protected function assertValidWriterClass($writerClass)
    {
        if (!isset($this->writers[$writerClass])) {
            throw new InvalidWriterException('Invalid writer "'.$writerClass.'"');
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
     * @throws UnsupportedExtensionException
     */
    public function getWriterByExtension($extension)
    {
        foreach ($this->writers as $writer) {
            if ($writer->supportsExtension($extension)) {
                return $writer;
            }
        }

        throw new UnsupportedExtensionException('Missing writer for extension "'.$extension.'"');
    }
}
