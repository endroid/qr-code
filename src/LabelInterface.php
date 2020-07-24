<?php

declare(strict_types=1);

namespace Endroid\QrCode;

interface LabelInterface
{
    public function getText(): string;

    public function getFont(): FontInterface;

    public function getAlignment(): LabelAlignment;
}
