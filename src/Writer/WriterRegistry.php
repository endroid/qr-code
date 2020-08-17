<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

final class WriterRegistry implements WriterRegistryInterface
{
    private array $writers;

    public function get(string $class)
    {
        if (!isset($this->writers[$class])) {
            throw new \Exception(sprintf('Writer "%s" is not registered: make sure it implements WriterInterface', $class));
        }

        return $this->writers[$class];
    }
}
