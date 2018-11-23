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

class LabelAlignment extends Enum
{
    const LEFT = 'left';
    const CENTER = 'center';
    const RIGHT = 'right';
}
