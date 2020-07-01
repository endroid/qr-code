<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

interface ValidatingWriterInterface extends WriterInterface
{
    public function setValidateResult(bool $validateResult): void;
}
