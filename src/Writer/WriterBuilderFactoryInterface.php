<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

interface WriterBuilderFactoryInterface
{
    public function create(): WriterBuilderInterface;
}
