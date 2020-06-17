<?php

declare(strict_types=1);

namespace Endroid\QrCode\Enum;

class ErrorCorrectionLevel extends AbstractEnum
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const QUARTILE = 'quartile';
    public const HIGH = 'high';
}
