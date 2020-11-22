<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Exception\QrCodeException;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\ResultInterface;
use Endroid\QrCode\Writer\WriterInterface;

final class Builder implements BuilderInterface
{
    private QrCodeInterface $qrCode;
    private LogoInterface $logo;
    private LabelInterface $label;
    private WriterInterface $writer;

    public static function create(): self
    {
        return new static();
    }

    public function qrCode(QrCodeInterface $qrCode): self
    {
        $this->qrCode = $qrCode;

        return $this;
    }

    public function logo(LogoInterface $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function label(LabelInterface $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function writer(WriterInterface $writer): self
    {
        $this->writer = $writer;

        return $this;
    }

    public function build(): ResultInterface
    {
        if (!isset($this->qrCode)) {
            throw new QrCodeException('Please provide a QR code via $builder->qrCode($qrCode)');
        }

        if (!isset($this->writer)) {
            throw new QrCodeException('Please provide a writer via $builder->writer($writer)');
        }

        $result = $this->writer->writeQrCode($this->qrCode);

        if (isset($this->logo) && $this->writer instanceof LogoWriterInterface) {
            $result = $this->writer->writeLogo($this->logo, $result);
        }

        if (isset($this->label) && $this->writer instanceof LabelWriterInterface) {
            $result = $this->writer->writeLabel($label, $result);
        }

        return $result;
    }
}
