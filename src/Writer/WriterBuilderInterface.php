<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Exception\QrCodeException;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeInterface;

interface WriterBuilderInterface
{
    public function build(): WriterInterface;
}
