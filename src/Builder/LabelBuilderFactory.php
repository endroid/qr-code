<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

class LabelBuilderFactory implements LabelBuilderFactoryInterface
{
    public function create(): LabelBuilder
    {
        return new LabelBuilder();
    }
}
