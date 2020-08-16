<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\LabelInterface;

interface LabelWriterInterface extends WriterInterface
{
    public function writeLabel(LabelInterface $label, ResultInterface $result);
}
