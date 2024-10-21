<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Writer\Result\ResultInterface;

interface BuilderInterface
{
    public function build(): ResultInterface;
}
