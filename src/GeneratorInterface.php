<?php

declare(strict_types=1);

namespace Endroid\QrCode;

interface GeneratorInterface
{
    public function setData(string $data): self;

    public function setErrorCorrectionLevel(string $errorCorrectionLevel): self;

    public function generate();
}
