<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label;

use Endroid\QrCode\Label\Alignment\Center;
use Endroid\QrCode\Label\Alignment\LabelAlignmentInterface;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Label\Margin\MarginInterface;

final class Label implements LabelInterface
{
    /** @var string */
    private $text;

    /** @var FontInterface */
    private $font;

    /** @var LabelAlignmentInterface */
    private $alignment;

    /** @var MarginInterface */
    private $margin;

    public function __construct(
        string $text,
        FontInterface $font = null,
        LabelAlignmentInterface $alignment = null,
        MarginInterface $margin = null
    ) {
        $this->text = $text;
        $this->font = isset($font) ? $font : new Font(__DIR__.'/../../assets/open_sans.ttf', 16);
        $this->alignment = isset($alignment) ? $alignment : new Center();
        $this->margin = isset($margin) ? $margin : new Margin(0, 10, 10, 10);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getFont(): FontInterface
    {
        return $this->font;
    }

    public function getAlignment(): LabelAlignmentInterface
    {
        return $this->alignment;
    }

    public function getMargin(): MarginInterface
    {
        return $this->margin;
    }
}
