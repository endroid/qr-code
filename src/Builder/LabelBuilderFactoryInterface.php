<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

interface LabelBuilderFactoryInterface
{
    public function create(): LabelBuilderInterface;
}
