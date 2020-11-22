<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label;

interface LabelBuilderFactoryInterface
{
    public function create(): LabelBuilderInterface;
}
