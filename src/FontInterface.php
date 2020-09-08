<?php

declare(strict_types=1);

namespace Endroid\QrCode;

interface FontInterface
{
    public function getPath(): string;

    public function getSize(): int;
}
