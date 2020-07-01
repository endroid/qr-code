<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\QrCodeInterface;
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

    public static function create(): self
    {
        return new self();
    }

    public function withWriter(string $writerClass): self
    {
        $new = clone $this;
        $new->writerClass = $writerClass;

        return $new;
    }

    public function withOptions(array $options): self
    {
        $new = clone $this;
        foreach ($options as $name => $value) {
            $new->options[$name] = $value;
        }

        return $new;
    }

    public function withData(string $data): self
    {
        return $this->withOptions(['data' => $data]);
    }

    public function getQrCode(): QrCodeInterface
    {
        $qrCode = new QrCode($this->options['data']);
        $this->applyOptions($qrCode);

        return $qrCode;
    }

    public function getWriter(): WriterInterface
    {
        $qrCode = $this->getQrCode();

        $writer = new $this->writerClass($qrCode);
        $this->applyOptions($writer);

        if ($writer instanceof LogoWriterInterface) {
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
