<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\FontInterface;
use Endroid\QrCode\Label;
use Endroid\QrCode\LabelAlignmentInterface;

class LabelBuilder implements LabelBuilderInterface
{
    private string $text;
    private FontInterface $font;
    private LabelAlignmentInterface $alignment;

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function font(FontInterface $font): self
    {
        $this->font = $font;

        return $this;
    }

    public function alignment(LabelAlignmentInterface $alignment): self
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function build(): Label
    {
        return new Label($this->text);
    }
}
