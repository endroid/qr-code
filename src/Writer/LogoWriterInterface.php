<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Logo\LogoInterface;

interface LogoWriterInterface
{
    public function writeLogo(LogoInterface $logo, ResultInterface $result): ResultInterface;
}
