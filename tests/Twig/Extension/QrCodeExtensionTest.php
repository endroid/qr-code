<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests\Twig\Extension;

use Endroid\QrCode\Factory\QrCodeFactory;
use Endroid\QrCode\Twig\Extension\QrCodeExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class QrCodeExtensionTest extends TestCase
{
    /**
     * @group legacy
     * @expectedDeprecation Using Endroid\QrCode\Twig\Extension\QrCodeExtension::qrCodeDataUriFunction is deprecated as of 3.1 and will be removed in 4.0. Use Endroid\QrCode\Twig\Extension\QrCodeUriExtension::qrCodeDataUriFunction() instead.
     */
    public function testQrCodeDataUriFunction()
    {
        $router = $this->prophesize(RouterInterface::class);
        $extension = new QrCodeExtension(new QrCodeFactory(), $router->reveal());
        $this->assertStringStartsWith('data:image/png;base64,', $extension->qrCodeDataUriFunction('Foobar'));
    }

    /**
     * @group legacy
     * @expectedDeprecation Using Endroid\QrCode\Twig\Extension\QrCodeExtension::qrCodePathFunction is deprecated as of 3.1 and will be removed in 4.0. Install the EndroidQrCodeBundle instead.
     */
    public function testQrCodePathFunction()
    {
        $router = $this->prophesize(RouterInterface::class);
        $extension = new QrCodeExtension(new QrCodeFactory(), $router->reveal());

        $router
            ->generate('endroid_qrcode_generate', ['extension' => 'png', 'text' => 'Foobar'], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/some-qr-code-path');

        $this->assertSame('/some-qr-code-path', $extension->qrCodePathFunction('Foobar'));
    }

    /**
     * @group legacy
     * @expectedDeprecation Using Endroid\QrCode\Twig\Extension\QrCodeExtension::qrCodeUrlFunction is deprecated as of 3.1 and will be removed in 4.0. Install the EndroidQrCodeBundle instead.
     */
    public function testQrCodeUrlFunction()
    {
        $router = $this->prophesize(RouterInterface::class);
        $extension = new QrCodeExtension(new QrCodeFactory(), $router->reveal());

        $router
            ->generate('endroid_qrcode_generate', ['extension' => 'png', 'text' => 'Foobar'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://some-qr-code-url');

        $this->assertSame('https://some-qr-code-url', $extension->qrCodeUrlFunction('Foobar'));
    }
}
