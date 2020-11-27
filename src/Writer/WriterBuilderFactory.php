<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Exception\QrCodeException;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeInterface;

class WriterBuilderFactory implements WriterBuilderFactoryInterface
{
    public function create(): WriterBuilderInterface
    {
        return new WriterBuilder();
    }
}
