<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Traits;

use BaconQrCode\Renderer\Color\Rgb;

trait BaconConversionTrait
{
    protected function convertColor(array $color): Rgb
    {
        $color = new Rgb($color['r'], $color['g'], $color['b']);

        return $color;
    }

    protected function convertErrorCorrectionLevel(string $errorCorrectionLevel): string
    {
        $name = strtoupper(substr($errorCorrectionLevel, 0, 1));
        $errorCorrectionLevel = constant('BaconQrCode\Common\ErrorCorrectionLevel::'.$name);

        return $errorCorrectionLevel;
    }
}
