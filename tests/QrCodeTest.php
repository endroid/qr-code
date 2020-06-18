<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Builder;
use Endroid\QrCode\Enum\LabelAlignment;
use Endroid\QrCode\Enum\ErrorCorrectionLevel;
use PHPUnit\Framework\TestCase;

class QrCodeTest extends TestCase
{
    public function testBuilder(): void
    {
        $builder = Builder::create()
            ->withData('Example data')
            ->withSize(300)
            ->withErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->withLabelAlignment(LabelAlignment::CENTER)
        ;

        $builder->build()->writeString();

        dump($result);
        die;
    }
}
