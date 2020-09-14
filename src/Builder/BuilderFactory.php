<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

class BuilderFactory implements BuilderFactoryInterface
{
    public function create(): Builder
    {
        return new Builder();
    }
}
