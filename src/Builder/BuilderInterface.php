<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Model\QrCodeInterface;
use Endroid\QrCode\Writer\WriterInterface;

interface BuilderInterface
{
    public static function create(): self;

    public function withWriter(string $class): self;

    public function withOptions(array $options): self;

    public function getQrCode(): QrCodeInterface;

    public function getWriter(): WriterInterface;
}
