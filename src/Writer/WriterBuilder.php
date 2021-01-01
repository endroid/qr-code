<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

final class WriterBuilder implements WriterBuilderInterface
{
    private string $className;

    public static function create(): self
    {
        return new static();
    }

    public function className(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function build(): WriterInterface
    {
        return new $this->className();
    }
}
