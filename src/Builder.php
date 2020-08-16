<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\WriterInterface;

class Builder
{
    private WriterInterface $writer;

    private string $data;
    private string $encoding;
    private string $errorCorrectionLevel;

    private string $logoPath;

    private string $labelText;
    private string $labelFontPath;
    private int $labelFontSize;
    private string $labelAlignment;

    public static function create(): self
    {
        return new self;
    }

    public function setWriter(WriterInterface $writer): self
    {
        $this->writer = $writer;
    }

    public function setData(string $data): self
    {
        $this->data = $data;
    }

    public function setEncoding(string $encoding): self
    {
        $this->encoding = $encoding;
    }

    public function setErrorCorrectionLevel(string $errorCorrectionLevel): self
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;
    }

    public function setLogoPath(string $logoPath): self
    {
        $this->logoPath = $logoPath;
    }

    public function setLabelText(string $labelText): self
    {
        $this->labelText = $labelText;
    }

    public function setLabelFontPath(string $labelFontPath): self
    {
        $this->labelFontPath = $labelFontPath;
    }

    public function setLabelFontSize(int $labelFontSize): self
    {
        $this->labelFontSize = $labelFontSize;
    }

    public function setLabelAlignment(string $labelAlignment): self
    {
        $this->labelAlignment = $labelAlignment;
    }

    public function build()
    {
        $qrCode = $this->buildQrCode();

        $result = $this->writer->writeQrCode($qrCode);

        if ($this->writer instanceof LogoWriterInterface) {
            $logo = $this->buildLogo();
            if ($logo instanceof LogoInterface) {
                $result = $this->writer->writeLogo($logo, $result);
            }
        }

        if ($this->writer instanceof LabelWriterInterface) {
            $label = $this->buildLabel();
            if ($label instanceof LabelInterface) {
                $result = $this->writer->writeLabel($label, $result);
            }
        }

        return $result;
    }

    public function buildQrCode(): QrCodeInterface
    {
        return new QrCode($this->data, new Encoding($this->encoding), new ErrorCorrectionLevel($this->errorCorrectionLevel));
    }

    public function buildLogo(): ?LogoInterface
    {
        return new Logo($this->logoPath);
    }

    public function buildLabel(): ?LabelInterface
    {
        return new Label($this->labelText, new Font($this->labelFontPath, $this->labelFontSize), new LabelAlignment($this->labelAlignment));
    }
}
