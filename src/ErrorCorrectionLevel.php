<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use MyCLabs\Enum\Enum;
use BaconQrCode\Common\ErrorCorrectionLevel as BaconErrorCorrectionLevel;

class ErrorCorrectionLevel extends Enum
{
    const LOW = 'low';
    const MEDIUM = 'medium';
    const QUARTILE = 'quartile';
    const HIGH = 'high';

    public function toBaconErrorCorrectionLevel(): BaconErrorCorrectionLevel
    {
        $name = strtoupper(substr($this->getValue(), 0, 1));

        return BaconErrorCorrectionLevel::valueOf($name);
    }
}
