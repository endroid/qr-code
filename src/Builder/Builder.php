<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Label\LabelBuilderFactory;
use Endroid\QrCode\Label\LabelBuilderFactoryInterface;
use Endroid\QrCode\Label\LabelBuilderInterface;
use Endroid\QrCode\Logo\LogoBuilderFactory;
use Endroid\QrCode\Logo\LogoBuilderFactoryInterface;
use Endroid\QrCode\Logo\LogoBuilderInterface;
use Endroid\QrCode\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Label\Alignment\LabelAlignmentInterface;
use Endroid\QrCode\QrCode\QrCodeBuilderFactory;
use Endroid\QrCode\QrCode\QrCodeBuilderFactoryInterface;
use Endroid\QrCode\QrCode\QrCodeBuilderInterface;
use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ResultInterface;
use Endroid\QrCode\Writer\WriterBuilder;
use Endroid\QrCode\Writer\WriterBuilderFactory;
use Endroid\QrCode\Writer\WriterBuilderFactoryInterface;
use Endroid\QrCode\Writer\WriterBuilderInterface;
use Endroid\QrCode\Writer\WriterInterface;

class Builder implements BuilderInterface
{
    private $qrCodeBuilderFactory;
    private $writerBuilderFactory;
    private $logoBuilderFactory;
    private $labelBuilderFactory;

    private $qrCodeBuilder;
    private $writerBuilder;
    private $logoBuilder;
    private $labelBuilder;

    public function __construct(
        QrCodeBuilderFactoryInterface $qrCodeBuilderFactory = null,
        WriterBuilderFactoryInterface $writerBuilderFactory = null,
        LogoBuilderFactoryInterface $logoBuilderFactory = null,
        LabelBuilderFactoryInterface $labelBuilderFactory = null
    ) {
        $this->qrCodeBuilderFactory = isset($qrCodeBuilderFactory) ? $qrCodeBuilderFactory : new QrCodeBuilderFactory();
        $this->writerBuilderFactory = isset($writerBuilderFactory) ? $writerBuilderFactory : new WriterBuilderFactory();
        $this->logoBuilderFactory = isset($logoBuilderFactory) ? $logoBuilderFactory : new LogoBuilderFactory();
        $this->labelBuilderFactory = isset($labelBuilderFactory) ? $labelBuilderFactory : new LabelBuilderFactory();
    }

    private function getWriterBuilder(): WriterBuilderInterface
    {
        if (!isset($this->writerBuilder)) {
            $this->writerBuilder = $this->writerBuilderFactory->create();
        }

        return $this->writerBuilder;
    }

    private function getQrCodeBuilder(): QrCodeBuilderInterface
    {
        if (!isset($this->qrCodeBuilder)) {
            $this->qrCodeBuilder = $this->qrCodeBuilderFactory->create();
        }

        return $this->qrCodeBuilder;
    }

    public function data(string $data): self
    {
        $this->getQrCodeBuilder()->data($data);

        return $this;
    }

    public function encoding(Encoding $encoding): self
    {
        $this->getQrCodeBuilder()->encoding($encoding);

        return $this;
    }

    public function errorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): self
    {
        $this->getQrCodeBuilder()->errorCorrectionLevel($errorCorrectionLevel);

        return $this;
    }

    private function ensureLogoBuilder(): void
    {
        if (!$this->logoBuilder instanceof LogoBuilderInterface) {
            $this->logoBuilder = $this->logoBuilderFactory->create();
        }
    }

    public function logoPath(string $logoPath): self
    {
        $this->ensureLogoBuilder();
        $this->logoBuilder->path($logoPath);

        return $this;
    }

    private function ensureLabelBuilder(): void
    {
        if (!$this->labelBuilder instanceof LabelBuilderInterface) {
            $this->labelBuilder = $this->labelBuilderFactory->create();
        }
    }

    public function labelText(string $labelText): self
    {
        $this->ensureLabelBuilder();
        $this->labelBuilder->text($labelText);

        return $this;
    }

    public function labelFont(FontInterface $labelFont): self
    {
        $this->ensureLabelBuilder();
        $this->labelBuilder->font($labelFont);

        return $this;
    }

    public function labelAlignment(LabelAlignmentInterface $labelAlignment): self
    {
        $this->ensureLabelBuilder();
        $this->labelBuilder->alignment($labelAlignment);

        return $this;
    }

    public function build(): ResultInterface
    {
        $result = $this->writer->writeQrCode($this->qrCodeBuilder->build());

        if ($this->writer instanceof LogoWriterInterface) {
            $logo = $this->logoBuilder->build();
            $result = $this->writer->writeLogo($logo, $result);
        }

        if ($this->writer instanceof LabelWriterInterface) {
            $label = $this->labelBuilder->build();
            $result = $this->writer->writeLabel($label, $result);
        }

        return $result;
    }
}
