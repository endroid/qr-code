<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\QrCode\QrCodeInterface;

final class BinaryWriter implements WriterInterface
{
    public function writeQrCode(QrCodeInterface $qrCode): ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        return new BinaryResult($matrix);
    }
}
