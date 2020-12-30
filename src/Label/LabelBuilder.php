<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label;

use Endroid\QrCode\Label\Alignment\LabelAlignmentInterface;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Label\Margin\Margin;

final class LabelBuilder implements LabelBuilderInterface
{
    /** @var string */
    private $text;
    private ?FontInterface $font = null;
    private ?LabelAlignmentInterface $alignment = null;
    private ?Margin $margin = null;

    public static function create(): self
    {
        return new static();
    }

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

    public function margin(Margin $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    public function build(): Label
    {
        return new Label($this->text, );
    }
}
