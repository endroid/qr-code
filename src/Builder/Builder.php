<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Encoding\EncodingInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;
use Endroid\QrCode\Label\Alignment\LabelAlignmentInterface;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Label\Margin\MarginInterface;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\ValidatingWriterInterface;
use Endroid\QrCode\Writer\WriterInterface;

final class Builder implements BuilderInterface
{
    /** @var array<mixed> */
    private $options;

    public function __construct()
    {
        $this->options = [
            'writer' => new PngWriter(),
            'writerOptions' => [],
            'qrCodeClass' => QrCode::class,
            'logoClass' => Logo::class,
            'labelClass' => Label::class,
            'validateResult' => false,
        ];
    }

    public static function create(): self
    {
        return new self();
    }

    public function writer(WriterInterface $writer): self
    {
        $this->options['writer'] = $writer;

        return $this;
    }

    /** @param array<mixed> $writerOptions */
    public function writerOptions(array $writerOptions): self
    {
        $this->options['writerOptions'] = $writerOptions;

        return $this;
    }

    public function data(string $data): self
    {
        $this->options['data'] = $data;

        return $this;
    }

    public function encoding(EncodingInterface $encoding): self
    {
        $this->options['encoding'] = $encoding;

        return $this;
    }

    public function errorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): self
    {
        $this->options['errorCorrectionLevel'] = $errorCorrectionLevel;

        return $this;
    }

    public function size(int $size): self
    {
        $this->options['size'] = $size;

        return $this;
    }

    public function margin(int $margin): self
    {
        $this->options['margin'] = $margin;

        return $this;
    }

    public function roundBlockSizeMode(RoundBlockSizeModeInterface $roundBlockSizeMode): self
    {
        $this->options['roundBlockSizeMode'] = $roundBlockSizeMode;

        return $this;
    }

    public function foregroundColor(ColorInterface $foregroundColor): self
    {
        $this->options['foregroundColor'] = $foregroundColor;

        return $this;
    }

    public function backgroundColor(ColorInterface $backgroundColor): self
    {
        $this->options['backgroundColor'] = $backgroundColor;

        return $this;
    }

    public function logoPath(string $logoPath): self
    {
        $this->options['logoPath'] = $logoPath;

        return $this;
    }

    public function logoResizeToWidth(string $logoResizeToWidth): self
    {
        $this->options['logoResizeToWidth'] = $logoResizeToWidth;

        return $this;
    }

    public function logoResizeToHeight(string $logoResizeToHeight): self
    {
        $this->options['logoResizeToHeight'] = $logoResizeToHeight;

        return $this;
    }

    public function labelText(string $labelText): self
    {
        $this->options['labelText'] = $labelText;

        return $this;
    }

    public function labelFont(FontInterface $labelFont): self
    {
        $this->options['labelFont'] = $labelFont;

        return $this;
    }

    public function labelAlignment(LabelAlignmentInterface $labelAlignment): self
    {
        $this->options['labelAlignment'] = $labelAlignment;

        return $this;
    }

    public function labelMargin(MarginInterface $labelMargin): self
    {
        $this->options['labelMargin'] = $labelMargin;

        return $this;
    }

    public function labelTextColor(ColorInterface $labelTextColor): self
    {
        $this->options['labelTextColor'] = $labelTextColor;

        return $this;
    }

    public function labelBackgroundColor(ColorInterface $labelBackgroundColor): self
    {
        $this->options['labelBackgroundColor'] = $labelBackgroundColor;

        return $this;
    }

    public function validateResult(bool $validateResult): self
    {
        $this->options['validateResult'] = $validateResult;

        return $this;
    }

    public function build(): ResultInterface
    {
        if (!isset($this->options['writer']) || !$this->options['writer'] instanceof WriterInterface) {
            throw new \Exception('Pass a valid writer via $builder->writer()');
        }

        $writer = $this->options['writer'];

        if ($this->options['validateResult'] && !$writer instanceof ValidatingWriterInterface) {
            throw new \Exception('Unable to validate result with '.get_class($writer));
        }

        /** @var QrCode $qrCode */
        $qrCode = $this->buildObject($this->options['qrCodeClass']);

        /** @var LogoInterface|null $logo */
        $logo = $this->buildObject($this->options['logoClass'], 'logo');

        /** @var LabelInterface|null $label */
        $label = $this->buildObject($this->options['labelClass'], 'label');

        $result = $writer->write($qrCode, $logo, $label, $this->options['writerOptions']);

        if ($this->options['validateResult'] && $writer instanceof ValidatingWriterInterface) {
            $writer->validateResult($result, $qrCode->getData());
        }

        return $result;
    }

    /**
     * @param class-string $class
     *
     * @return mixed
     */
    private function buildObject(string $class, string $optionsPrefix = null)
    {
        /** @var \ReflectionClass<object> $reflectionClass */
        $reflectionClass = new \ReflectionClass($class);

        $arguments = [];
        $hasBuilderOptions = false;
        $missingRequiredArguments = [];
        /** @var \ReflectionMethod $constructor */
        $constructor = $reflectionClass->getConstructor();
        $constructorParameters = $constructor->getParameters();
        foreach ($constructorParameters as $parameter) {
            $optionName = null === $optionsPrefix ? $parameter->getName() : $optionsPrefix.ucfirst($parameter->getName());
            if (isset($this->options[$optionName])) {
                $hasBuilderOptions = true;
                $arguments[] = $this->options[$optionName];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $missingRequiredArguments[] = $optionName;
            }
        }

        if (!$hasBuilderOptions) {
            return null;
        }

        if (count($missingRequiredArguments) > 0) {
            throw new \Exception(sprintf('Missing required arguments: %s', implode(', ', $missingRequiredArguments)));
        }

        return $reflectionClass->newInstanceArgs($arguments);
    }
}
