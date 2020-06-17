<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Writer\ImageWriterInterface;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Generator implements GeneratorInterface
{
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function withData(string $data): self
    {
        $new = clone $this;
        $new->options[QrCodeInterface::class]['data'] = $data;

        return $new;
    }

    public function withErrorCorrectionLevel(string $errorCorrectionLevel): self
    {
        $new = clone $this;
        $new->options[QrCodeInterface::class]['errorCorrectionLevel'] = $errorCorrectionLevel;

        return $new;
    }

    public function withSize(int $size): self
    {
        $new = clone $this;
        $new->options[ImageWriterInterface::class]['size'] = $size;

        return $new;
    }

    public function withLabelAlignment(string $labelAlignment): self
    {
        $new = clone $this;
        $new->options[LabelInterface::class]['label_alignment'] = $labelAlignment;

        return $new;
    }

    public function generate()
    {
        $qrCode = new QrCode($this->options['data']);
        $this->applyOptions($qrCode);

        $logo = new Logo($this->options['logo']);
        $this->applyOptions($logo);

        $label = new Label($this->options['label']);
        $this->applyOptions($label);

        $writer = new PngWriter($qrCode);
        $writer->setLogo($logo);
        $writer->setLabel($label);
        $this->applyOptions($writer);
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
