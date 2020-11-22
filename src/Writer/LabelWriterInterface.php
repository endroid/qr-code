<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label\LabelInterface;

interface LabelWriterInterface
{
    public function writeLabel(LabelInterface $label, ResultInterface $result): ResultInterface;
}
