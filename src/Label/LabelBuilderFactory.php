<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label;

final class LabelBuilderFactory implements LabelBuilderFactoryInterface
{
    public function create(): LabelBuilder
    {
        return new LabelBuilder();
    }
}
