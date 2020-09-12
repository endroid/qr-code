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
    private WriterInterface $writer;
    private QrCodeBuilderInterface $qrCodeBuilder;
    private LogoBuilderInterface $logoBuilder;
    private LabelBuilderInterface $labelBuilder;

    public function __construct(
        QrCodeBuilderInterface $qrCodeBuilder = null,
        LogoBuilderInterface $logoBuilder = null,
        LabelBuilderInterface $labelBuilder = null
    ) {
        $this->writer = new PngWriter();
        $this->qrCodeBuilder = $qrCodeBuilder instanceof QrCodeBuilderInterface ? $qrCodeBuilder : new QrCodeBuilder();
        $this->logoBuilder = $logoBuilder instanceof LogoBuilderInterface ? $logoBuilder : new LogoBuilder();
        $this->labelBuilder = $labelBuilder instanceof LabelBuilderInterface ? $labelBuilder : new LabelBuilder();
    }

    public function writer(WriterInterface $writer): self
    {
        $this->writer = $writer;

        return $this;
    }

    public function data(string $data): self
    {
        $this->qrCodeBuilder->data($data);

        return $this;
    }

    public function encoding(Encoding $encoding): self
    {
        $this->qrCodeBuilder->encoding($encoding);

        return $this;
    }

    public function errorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): self
    {
        $this->qrCodeBuilder->errorCorrectionLevel($errorCorrectionLevel);

        return $this;
    }

    public function logoPath(string $logoPath): self
    {
        $this->logoBuilder->path($logoPath);

        return $this;
    }

    public function labelText(string $labelText): self
    {
        $this->labelBuilder->text($labelText);

        return $this;
    }

    public function labelFont(FontInterface $labelFont): self
    {
        $this->labelBuilder->font($labelFont);

        return $this;
    }

    public function labelAlignment(LabelAlignmentInterface $labelAlignment): self
    {
        $this->labelBuilder->alignment($labelAlignment);

        return $this;
    }

    public function getResult(): ResultInterface
    {
        $result = $this->writer->writeQrCode($this->qrCodeBuilder->getResult());

        if ($this->writer instanceof LogoWriterInterface) {
            $logo = $this->logoBuilder->getResult();
            $result = $this->writer->writeLogo($logo, $result);
        }

        if ($this->writer instanceof LabelWriterInterface) {
            $label = $this->labelBuilder->getResult();
            $result = $this->writer->writeLabel($label, $result);
        }

        return $result;
    }
}
