<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\String\UnicodeString;

class QrCodeTest extends TestCase
{
    /**
     * @testdox The QR code can be created
     */
    public function testQrCode(): void
    {
//        $qrCode = new \Endroid\QrCode\QrCode('test');
//        $label = new \Endroid\QrCode\Label('label');
//        $logo = new \Endroid\QrCode\Logo(__DIR__.'/../../../../../vendor/endroid/qr-code/tests/assets/symfony.png');
//
//        $this->

        die('z');

        $data = new UnicodeString('test');

        dump($data);
        die;

    }

}
