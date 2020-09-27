<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\LabelInterface;
use Endroid\QrCode\LogoInterface;
use Endroid\QrCode\QrCodeInterface;

class EpsWriter implements LogoWriterInterface, LabelWriterInterface
{
    public function writeQrCode(QrCodeInterface $qrCode): EpsResult
    {
        // @todo write QR code

        return new EpsResult();
    }

    public function writeLabel(LabelInterface $label, ResultInterface $result): EpsResult
    {
        if (!$result instanceof EpsResult) {
            throw new \Exception('EpsWriter only supports EpsResult instances');
        }

        // @todo write label to EPS

        return $result;
    }

    public function writeLogo(LogoInterface $logo, ResultInterface $result): EpsResult
    {
        if (!$result instanceof EpsResult) {
            throw new \Exception('EpsWriter only supports EpsResult instances');
        }

        // @todo write logo to EPS

        return $result;
    }
}
