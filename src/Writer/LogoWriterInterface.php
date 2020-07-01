<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Logo;

interface LogoWriterInterface extends WriterInterface
{
    public function setLogo(Logo $logo): void;
}
