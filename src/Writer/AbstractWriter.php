<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;

abstract class AbstractWriter implements WriterInterface
{
    private $qrCode;

    public function __construct(QrCodeInterface $qrCode)
    {
        $this->qrCode = $qrCode;
    }

    public function writeDataUri(): string
    {
        return 'data:'.$this->getMimeType().';base64,'.base64_encode($this->writeString());
    }

    public function writeFile(string $path): void
    {
        $string = $this->writeString();
        file_put_contents($path, $string);
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }
}
