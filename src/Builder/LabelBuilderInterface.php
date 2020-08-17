<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\LabelInterface;

interface LabelBuilderInterface
{
    public function text(string $text): self;
    public function build(): LabelInterface;
}
