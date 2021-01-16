<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

interface ValidatingWriterInterface
{
    public function validateResult(ResultInterface $result, string $expectedData): void;
}
