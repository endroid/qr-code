<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Logo;

trait LogoWriterTrait
{
    private $logo;

    public function setLogo(Logo $logo): void
    {
        $this->logo = $logo;
    }
}
