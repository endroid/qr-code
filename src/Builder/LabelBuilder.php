<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Label;

class LabelBuilder implements LabelBuilderInterface
{
    private string $text;

    public function text(string $text): self
    {
        $this->text = $text;
    }

    public function build(): Label
    {
        return new Label($this->text);
    }
}
