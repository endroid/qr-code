<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Label\Margin\MarginInterface;

final class Label implements LabelInterface
{
    public function __construct(
        private string $text,
        private FontInterface $font = new Font(__DIR__.'/../../assets/noto_sans.otf', 16),
        private LabelAlignment $alignment = LabelAlignment::Center,
        private MarginInterface $margin = new Margin(0, 10, 10, 10),
        private ColorInterface $textColor = new Color(0, 0, 0)
    ) {
    }

    public static function create(string $text): self
    {
        return new self($text);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getFont(): FontInterface
    {
        return $this->font;
    }

    public function setFont(FontInterface $font): self
    {
        $this->font = $font;

        return $this;
    }

    public function getAlignment(): LabelAlignment
    {
        return $this->alignment;
    }

    public function setAlignment(LabelAlignment $alignment): self
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function getMargin(): MarginInterface
    {
        return $this->margin;
    }

    public function setMargin(MarginInterface $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    public function getTextColor(): ColorInterface
    {
        return $this->textColor;
    }

    public function setTextColor(ColorInterface $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }
}
