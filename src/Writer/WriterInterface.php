<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCode\QrCodeInterface;

interface WriterInterface
{
    public function writeQrCode(QrCodeInterface $qrCode): ResultInterface;
}
