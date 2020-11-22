<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label;

use Endroid\QrCode\Label\Alignment\LabelAlignmentInterface;
use Endroid\QrCode\Label\Font\FontInterface;

interface LabelBuilderInterface
{
    public function text(string $text): self;

    public function font(FontInterface $font): self;

    public function alignment(LabelAlignmentInterface $alignment): self;

    public function build(): LabelInterface;
}
