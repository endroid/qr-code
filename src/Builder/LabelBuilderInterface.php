<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\FontInterface;
use Endroid\QrCode\LabelAlignmentInterface;
use Endroid\QrCode\LabelInterface;

interface LabelBuilderInterface
{
    public function text(string $text): self;
    public function font(FontInterface $font): self;
    public function alignment(LabelAlignmentInterface $alignment): self;
    public function build(): LabelInterface;
}
