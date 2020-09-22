<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Encoding;
use Endroid\QrCode\ErrorCorrectionLevelInterface;
use Endroid\QrCode\FontInterface;
use Endroid\QrCode\LabelAlignmentInterface;
use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ResultInterface;
use Endroid\QrCode\Writer\WriterInterface;

class Builder implements BuilderInterface
{
    private QrCodeBuilderFactoryInterface $qrCodeBuilderFactory;
    private LogoBuilderFactoryInterface $logoBuilderFactory;
    private LabelBuilderFactoryInterface $labelBuilderFactory;

    private ?QrCodeBuilderInterface $qrCodeBuilder = null;
    private ?LogoBuilderInterface $logoBuilder = null;
    private ?LabelBuilderInterface $labelBuilder = null;

    private WriterInterface $writer;

    public function __construct(
        QrCodeBuilderFactoryInterface $qrCodeBuilderFactory = null,
        LogoBuilderFactoryInterface $logoBuilderFactory = null,
        LabelBuilderFactoryInterface $labelBuilderFactory= null
    ) {
        $this->qrCodeBuilderFactory = $qrCodeBuilderFactory === null ? new QrCodeBuilderFactory() : $qrCodeBuilderFactory;
        $this->logoBuilderFactory = $logoBuilderFactory === null ? new LogoBuilderFactory() : $logoBuilderFactory;
        $this->labelBuilderFactory = $labelBuilderFactory === null ? new LabelBuilderFactory() : $labelBuilderFactory;

        $this->writer = new PngWriter();
    }

    public function writer(WriterInterface $writer): self
    {
        $this->writer = $writer;

        return $this;
    }

    private function ensureQrCodeBuilder(): void
    {
        if (!$this->qrCodeBuilder instanceof QrCodeBuilderInterface) {
            $this->qrCodeBuilder = $this->qrCodeBuilderFactory->create();
        }
    }

    public function data(string $data): self
    {
        $this->ensureQrCodeBuilder();
        $this->qrCodeBuilder->data($data);

        return $this;
    }

    public function encoding(Encoding $encoding): self
    {
        $this->ensureQrCodeBuilder();
        $this->qrCodeBuilder->encoding($encoding);

        return $this;
    }

    public function errorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): self
    {
        $this->ensureQrCodeBuilder();
        $this->qrCodeBuilder->errorCorrectionLevel($errorCorrectionLevel);

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
