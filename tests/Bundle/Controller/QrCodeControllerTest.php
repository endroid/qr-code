<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QrCodeControllerTest extends WebTestCase
{
    /**
     * Tests if the QR code generation route returns a success response.
     */
    public function testCreateQrCode()
    {
        $client = static::createClient();

        $client->request('GET', $client->getContainer()->get('router')->generate('endroid_qrcode', [
            'text' => 'Life is too short to be generating QR codes',
            'extension' => 'png',
            'size' => 150,
            'label' => 'Dit is een label',
            'label_font_size' => 16,
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
