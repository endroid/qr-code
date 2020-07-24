<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use Endroid\QrCode\Enum\AbstractEnum;

final class ErrorCorrectionLevel extends AbstractEnum
{
    public static function values(): array
    {
        return ['low', 'medium', 'quartile', 'high'];
    }
}
