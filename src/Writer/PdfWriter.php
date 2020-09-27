<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\LabelInterface;
use Endroid\QrCode\LogoInterface;
use Endroid\QrCode\QrCodeInterface;

class PdfWriter implements LogoWriterInterface, LabelWriterInterface
{
    public function writeQrCode(QrCodeInterface $qrCode): PdfResult
    {
        // @todo Write PDF QR code

        return new PdfResult();
    }

    public function writeLabel(LabelInterface $label, ResultInterface $result): PdfResult
    {
        if (!$result instanceof PdfResult) {
            throw new \Exception('PdfWriter only supports PdfResult instances');
        }

        // TODO: Implement writeLabel() method.

        return $result;
    }

    public function writeLogo(LogoInterface $logo, ResultInterface $result): PdfResult
    {
        if (!$result instanceof PdfResult) {
            throw new \Exception('PdfWriter only supports PdfResult instances');
        }

        // TODO: Implement writeLogo() method.

        return $result;
    }
}
