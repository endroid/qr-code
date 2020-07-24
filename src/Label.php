<?php

declare(strict_types=1);

namespace Endroid\QrCode;

final class Label implements LabelInterface
{
    private string $text;
    private FontInterface $font;
    private LabelAlignment $alignment;

    public function __construct(string $text, FontInterface $font = null, LabelAlignment $alignment = null)
    {
        $this->text = $text;
        $this->font = is_null($font) ? new Font(__DIR__.'/../assets/open_sans.ttf', 16) : $font;
        $this->alignment = is_null($alignment) ? new LabelAlignment('center') : $alignment;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getFont(): FontInterface
    {
        return $this->font;
    }

    public function getAlignment(): LabelAlignment
    {
        return $this->alignment;
    }
}
