<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

interface WriterRegistryInterface
{
    public function get(string $class);
}
