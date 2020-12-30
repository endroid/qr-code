<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

interface WriterBuilderInterface
{
    public function build(): WriterInterface;
}
