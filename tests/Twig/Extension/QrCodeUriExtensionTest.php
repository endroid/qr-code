<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests\Twig\Extension;

use Endroid\QrCode\Factory\QrCodeFactory;
use Endroid\QrCode\Twig\Extension\QrCodeUriExtension;
use PHPUnit\Framework\TestCase;

class QrCodeUriExtensionTest extends TestCase
{
    public function testQrCodeDataUriFunction()
    {
        $extension = new QrCodeUriExtension(new QrCodeFactory());
        $this->assertStringStartsWith('data:image/png;base64,', $extension->qrCodeDataUriFunction('Foobar'));
    }
}
