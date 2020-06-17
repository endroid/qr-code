<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

interface WriterInterface
{
    public function writeString(): string;

    public function writeDataUri(): string;

    public function writeFile(string $path): void;

    public function getMimeType(): string;
}
