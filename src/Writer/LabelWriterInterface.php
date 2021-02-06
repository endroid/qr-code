<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;

interface LabelWriterInterface
{
    /** @param array<mixed> $options */
    public function writeLabel(LabelInterface $label, ResultInterface $result, array $options = []): ResultInterface;
}
