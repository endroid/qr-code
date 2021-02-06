<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;

interface LogoWriterInterface
{
    /** @param array<mixed> $options */
    public function writeLogo(LogoInterface $logo, ResultInterface $result, array $options = []): ResultInterface;
}
