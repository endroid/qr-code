<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

use Endroid\QrCode\Color\ColorInterface;

interface LogoInterface
{
    public function getPath(): string;

    public function getResizeToWidth(): int|null;

    public function getResizeToHeight(): int|null;

    public function getPunchoutBackground(): bool;

    public function getMargin(): int;

    public function getBackgroundColor(): ColorInterface;
}
