<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Enum\AbstractEnum;

final class LabelAlignment extends AbstractEnum
{
    public static function values(): array
    {
        return ['left', 'center', 'right'];
    }
}
