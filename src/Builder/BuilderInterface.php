<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\ResultInterface;
use Endroid\QrCode\Writer\WriterInterface;

interface BuilderInterface
{
    public function qrCode(QrCodeInterface $qrCode): self;

    public function logo(LogoInterface $logo): self;

    public function label(LabelInterface $label): self;

    public function writer(WriterInterface $writer): self;

    public function build(): ResultInterface;
}
