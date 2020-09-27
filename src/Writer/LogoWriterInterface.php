<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\LogoInterface;

interface LogoWriterInterface extends WriterInterface
{
    public function writeLogo(LogoInterface $logo, ResultInterface $result): ResultInterface;
}
