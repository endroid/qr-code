<?php

declare(strict_types=1);

namespace Endroid\QrCode;

class Label implements LabelInterface
{
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }
}
