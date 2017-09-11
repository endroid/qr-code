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
use Endroid\QrCode\Writer\WriterInterface;

class QrCode implements QrCodeInterface
{
    const LABEL_FONT_PATH_DEFAULT = __DIR__ . '/../assets/noto_sans.otf';

    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $size = 300;

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
    protected $logoPath;

    /**
     * @var int
     */
    protected $logoWidth;

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
        'r' => 10,
        'b' => 10,
        'l' => 10,
    ];

    /**
     * @var WriterRegistryInterface
     */
    protected $writerRegistry;

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * @var bool
     */
    protected $validateResult = false;

    /**
     * @param string $text
     */
    public function __construct($text = '')
    {
        $this->text = $text;

        $this->errorCorrectionLevel = new ErrorCorrectionLevel(ErrorCorrectionLevel::LOW);
        $this->labelAlignment = new LabelAlignment(LabelAlignment::CENTER);

        $this->writerRegistry = new StaticWriterRegistry();
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getErrorCorrectionLevel()
    {
        return $this->errorCorrectionLevel->getValue();
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
     * {@inheritdoc}
     */
    public function getLogoPath()
    {
        return $this->logoPath;
    }

    /**
     * @param int $logoWidth
     * @return $this
     */
    public function setLogoWidth($logoWidth)
    {
        $this->logoWidth = $logoWidth;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoWidth()
    {
        return $this->logoWidth;
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getLabelAlignment()
    {
        return $this->labelAlignment->getValue();
    }

    /**
     * @param int[] $labelMargin
     * @return $this
     */
    public function setLabelMargin(array $labelMargin)
    {
        $this->labelMargin = array_merge($this->labelMargin, $labelMargin);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelMargin()
    {
        return $this->labelMargin;
    }

    /**
     * @param WriterRegistryInterface $writerRegistry
     * @return $this
     */
    public function setWriterRegistry(WriterRegistryInterface $writerRegistry)
    {
        $this->writerRegistry = $writerRegistry;

        return $this;
    }

    /**
     * @param WriterInterface $writer
     * @return $this
     */
    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;

        return $this;
    }

    /**
     * @param WriterInterface $name
     * @return WriterInterface
     */
    public function getWriter($name = null)
    {
        if (!is_null($name)) {
            return $this->writerRegistry->getWriter($name);
        }

        if ($this->writer instanceof WriterInterface) {
            return $this->writer;
        }

        return $this->writerRegistry->getDefaultWriter();
    }

    /**
     * @param string $name
     * @return $this
     * @throws InvalidWriterException
     */
    public function setWriterByName($name)
    {
        $this->writer = $this->writerRegistry->getWriter($name);

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setWriterByPath($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        $this->setWriterByExtension($extension);

        return $this;
    }

    /**
     * @param string $extension
     * @return $this
     * @throws UnsupportedExtensionException
     */
    public function setWriterByExtension($extension)
    {
        foreach ($this->writerRegistry->getWriters() as $writer) {
            if ($writer->supportsExtension($extension)) {
                $this->writer = $writer;
                return $this;
            }
        }

        throw new UnsupportedExtensionException('Missing writer for extension "'.$extension.'"');
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
     * {@inheritdoc}
     */
    public function getValidateResult()
    {
        return $this->validateResult;
    }

    /**
     * @return string
     */
    public function writeString()
    {
        return $this->getWriter()->writeString($this);
    }

    /**
     * @return string
     */
    public function writeDataUri()
    {
        return $this->getWriter()->writeDataUri($this);
    }

    /**
     * @param string $path
     */
    public function writeFile($path)
    {
        return $this->getWriter()->writeFile($this, $path);
    }

    /**
     * @return string
     * @throws InvalidWriterException
     */
    public function getContentType()
    {
        return $this->getWriter()->getContentType();
    }
}
