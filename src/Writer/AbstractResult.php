<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

abstract class AbstractResult implements ResultInterface
{
    abstract public function getString(): string;

    public function getDataUri(): string
    {
        return 'data:'.$this->getMimeType().';base64,'.base64_encode($this->getString());
    }

    public function saveToFile(string $path): void
    {
        $string = $this->getString();
        file_put_contents($path, $string);
    }

    abstract public function getMimeType(): string;
}
