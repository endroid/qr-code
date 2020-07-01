<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label;

trait LabelWriterTrait
{
    private $label;

    public function setLabel(Label $label): void
    {
        $this->label = $label;
    }
}
