<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;

interface WriterInterface
{
    /** @param array<mixed> $options */
    public function writeQrCode(QrCodeInterface $qrCode, array $options = []): ResultInterface;
}
