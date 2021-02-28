<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

abstract class AbstractResult implements ResultInterface
{
    public function getDataUri(): string
    {
        return 'data:'.$this->getMimeType().';base64,'.base64_encode($this->getString());
    }

    public function saveToFile(string $path): void
    {
        $string = $this->getString();
        file_put_contents($path, $string);
    }
}
