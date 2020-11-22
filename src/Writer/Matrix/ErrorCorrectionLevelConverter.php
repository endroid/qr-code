<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Matrix;

use BaconQrCode\Common\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\QrCodeException;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\High;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\Low;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\Medium;
use Endroid\QrCode\QrCode\ErrorCorrectionLevel\Quartile;

final class ErrorCorrectionLevelConverter
{
    public static function convertToBaconErrorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): ErrorCorrectionLevel
    {
        if ($errorCorrectionLevel instanceof Low) {
            return ErrorCorrectionLevel::valueOf('L');
        } elseif ($errorCorrectionLevel instanceof Medium) {
            return ErrorCorrectionLevel::valueOf('M');
        } elseif ($errorCorrectionLevel instanceof Quartile) {
            return ErrorCorrectionLevel::valueOf('Q');
        } elseif ($errorCorrectionLevel instanceof High) {
            return ErrorCorrectionLevel::valueOf('H');
        }

        throw new QrCodeException('Error correction level could not be converted');
    }
}
