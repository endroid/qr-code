<?php

declare(strict_types=1);

namespace Endroid\QrCode;

enum RoundBlockSizeMode: string
{
    case Enlarge = 'enlarge';
    case Margin = 'margin';
    case None = 'none';
    case Shrink = 'shrink';
}
