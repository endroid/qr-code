<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Enum\LabelAlignment;
use Endroid\QrCode\Generator;
use Endroid\QrCode\Enum\ErrorCorrectionLevel;
use PHPUnit\Framework\TestCase;

class QrCodeTest extends TestCase
{
    public function testGenerateQrCode(): void
    {
        $generator = new Generator();
        $generator
            ->setData('Example data')
            ->setSize(300)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setLabelAlignment(LabelAlignment::CENTER)
        ;

        $result = $generator->generate();

        dump($result);
        die;
    }
}
