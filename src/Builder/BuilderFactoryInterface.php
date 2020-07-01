<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

interface BuilderFactoryInterface
{
    public function create(string $name): BuilderInterface;
}
