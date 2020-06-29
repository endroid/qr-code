<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Writer\WriterInterface;

interface BuilderInterface
{
    public static function create(): self;

    public function withOptions(array $options): self;

    public function withData(string $data): self;

    public function withErrorCorrectionLevel(string $errorCorrectionLevel): self;

    public function getQrCode(): QrCodeInterface;

    public function getWriter(): WriterInterface;
}
