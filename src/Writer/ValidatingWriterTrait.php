<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

trait ValidatingWriterTrait
{
    private $validateResult;

    public function setValidateResult(bool $validateResult): void
    {
        $this->validateResult = $validateResult;
    }
}
