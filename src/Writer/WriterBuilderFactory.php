<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

class WriterBuilderFactory implements WriterBuilderFactoryInterface
{
    public function create(): WriterBuilderInterface
    {
        return new WriterBuilder();
    }
}
