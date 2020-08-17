<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ResultInterface;
use Endroid\QrCode\Writer\WriterInterface;

class Builder
{
    private QrCodeBuilderInterface $qrCodeBuilder;
    private LogoBuilderInterface $logoBuilder;
    private LabelBuilderInterface $labelBuilder;
    private WriterInterface $writer;

    public function __construct()
    {
        $this->qrCodeBuilder = new QrCodeBuilder();
        $this->logoBuilder = new LogoBuilder();
        $this->labelBuilder = new LabelBuilder();
        $this->writer = new PngWriter();
    }

    public function withQrCode(): QrCodeBuilderInterface
    {
        return $this->qrCodeBuilder;
    }

    public function withLogo(): LogoBuilderInterface
    {
        return $this->logoBuilder;
    }

    public function withLabel(): LabelBuilderInterface
    {
        return $this->labelBuilder;
    }

    public function build(): ResultInterface
    {
        $qrCode = $this->qrCodeBuilder->build();
        $result = $this->writer->writeQrCode($qrCode);

        if ($this->logoEnabled && $this->writer instanceof LogoWriterInterface) {
            $result = $this->writer->writeLogo($this->buildLogo(), $result);
        }

        if ($this->labelEnabled && $this->writer instanceof LabelWriterInterface) {
            $result = $this->writer->writeLabel($this->buildLabel(), $result);
        }

        return $result;
    }

    public function buildQrCode(): QrCodeInterface
    {
        return new QrCode($this->data, new Encoding($this->encoding), new ErrorCorrectionLevel($this->errorCorrectionLevel));
    }

    public function buildLogo(): LogoInterface
    {
        return new Logo($this->logoPath);
    }

    public function buildLabel(): LabelInterface
    {
        return new Label($this->labelText, new Font($this->labelFontPath, $this->labelFontSize), new LabelAlignment($this->labelAlignment));
    }
}
