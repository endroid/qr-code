<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

interface BuilderRegistryInterface
{
    public function set(string $name, BuilderInterface $builder): void;

    public function get(string $name): BuilderInterface;
}
