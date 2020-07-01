<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label;

interface LabelWriterInterface extends WriterInterface
{
    public function setLabel(Label $label): void;
}
