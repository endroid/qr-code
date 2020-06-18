<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\LabelInterface;
use Endroid\QrCode\LogoInterface;

abstract class AbstractImageWriter extends AbstractWriter implements ImageWriterInterface
{
    private $size = 300;
    private $margin = 10;
    private $logo;
    private $label;

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function setMargin(int $margin): void
    {
        $this->margin = $margin;
    }

    public function setLogo(LogoInterface $logo): void
    {
        $this->logo = $logo;
    }

    public function setLabel(LabelInterface $label): void
    {
        $this->label = $label;
    }
}
