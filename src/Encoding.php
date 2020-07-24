<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Enum\AbstractEnum;

final class Encoding extends AbstractEnum
{
    public static function values(): array
    {
        return mb_list_encodings();
    }
}
