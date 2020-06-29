<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Writer\ImageWriterInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\WriterInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Builder implements BuilderInterface
{
    private $writerClass = PngWriter::class;
    private $options;

    private function __construct()
    {
        $this->options = [];
    }

    public static function create(array $options = []): self
    {
        $new = new self();

        return $new->withOptions($options);
    }

    public function withWriter(string $writerClass): self
    {
        $new = clone $this;
        $new->writerClass = $writerClass;

        return $new;
    }

    private function withOptions(array $options): self
    {
        $new = clone $this;
        foreach ($options as $class => $classOptions) {
            foreach ($classOptions as $name => $value) {
                $new = $this->withOption($class, $name, $value);
            }
        }

        return $new;
    }

    private function withOption(string $class, string $name, $value): self
    {
        $new = clone $this;
        $new->options[$class][$name] = $value;

        return $new;
    }

    public function withData(string $data): self
    {
        return $this->withOption(QrCodeInterface::class, 'data', $data);
    }

    public function withErrorCorrectionLevel(string $errorCorrectionLevel): self
    {
        return $this->withOption(QrCodeInterface::class, 'error_correction_level', $errorCorrectionLevel);
    }

    public function withSize(int $size): self
    {
        return $this->withOption(ImageWriterInterface::class, 'size', $size);
    }

    public function withLabelAlignment(string $labelAlignment): self
    {
        return $this->withOption(LabelInterface::class, 'label_alignment', $labelAlignment);
    }

    public function getQrCode(): QrCodeInterface
    {
        $qrCode = new QrCode($this->options['data']);
        $this->applyOptions($qrCode);
    }

    public function getWriter(): WriterInterface
    {
        $qrCode = $this->getQrCode();

        $writer = new $this->writerClass($qrCode);
        $this->applyOptions($writer);

        if ($writer instanceof ImageWriterInterface) {
            $logo = new Logo($this->options['logo']);
            $this->applyOptions($logo);
            $writer->setLogo($logo);
        }

        if ($writer instanceof ImageWriterInterface) {
            $label = new Label($this->options['label']);
            $this->applyOptions($label);
            $writer->setLabel($label);
        }

        return $writer;
    }

    private function applyOptions($object): void
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->options as $class => $classOptions) {
            if ($object instanceof $class) {
                foreach ($classOptions as $name => $value) {
                    $accessor->setValue($object, $name, $value);
                }
            }
        }
    }
}
