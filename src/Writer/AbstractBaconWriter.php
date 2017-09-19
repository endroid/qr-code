<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use BaconQrCode\Renderer\Color\Rgb;

abstract class AbstractBaconWriter extends AbstractWriter
{
    /**
     * @param array $color
     *
     * @return Rgb
     */
    protected function convertColor(array $color)
    {
        $color = new Rgb($color['r'], $color['g'], $color['b']);

        return $color;
    }

    /**
     * @param string $errorCorrectionLevel
     *
     * @return string
     */
    protected function convertErrorCorrectionLevel($errorCorrectionLevel)
    {
        $name = strtoupper(substr($errorCorrectionLevel, 0, 1));
        $errorCorrectionLevel = constant('BaconQrCode\Common\ErrorCorrectionLevel::'.$name);

        return $errorCorrectionLevel;
    }
}
