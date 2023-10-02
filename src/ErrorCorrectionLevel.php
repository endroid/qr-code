<?php

declare(strict_types=1);

namespace Endroid\QrCode;

enum ErrorCorrectionLevel: string
{
    case High = 'high';
    case Low = 'low';
    case Medium = 'medium';
    case Quartile = 'quartile';
}
