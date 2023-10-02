<?php

declare(strict_types=1);

namespace Endroid\QrCode\Bacon;

use BaconQrCode\Common\ErrorCorrectionLevel as BaconErrorCorrectionLevel;
use Endroid\QrCode\ErrorCorrectionLevel;

final class ErrorCorrectionLevelConverter
{
    public static function convertToBaconErrorCorrectionLevel(ErrorCorrectionLevel $errorCorrectionLevel): BaconErrorCorrectionLevel
    {
        return match ($errorCorrectionLevel) {
            ErrorCorrectionLevel::Low => BaconErrorCorrectionLevel::valueOf('L'),
            ErrorCorrectionLevel::Medium => BaconErrorCorrectionLevel::valueOf('M'),
            ErrorCorrectionLevel::Quartile => BaconErrorCorrectionLevel::valueOf('Q'),
            ErrorCorrectionLevel::High => BaconErrorCorrectionLevel::valueOf('H')
        };
    }
}
