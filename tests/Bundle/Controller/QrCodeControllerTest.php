<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class QrCodeControllerTest extends WebTestCase
{
    public function testGenerateAction()
    {
        $client = static::createClient();
        $client->request('GET', $client->getContainer()->get('router')->generate('endroid_qrcode_generate', [
            'text' => 'Life is too short to be generating QR codes',
            'extension' => 'png',
            'size' => 200,
            'margin' => 10,
            'label' => 'Scan the code',
            'label_font_size' => 16,
        ]));

        $response = $client->getResponse();
        $image = imagecreatefromstring($response->getContent());

        $this->assertTrue(220 == imagesx($image));
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testTwigFunctionsAction()
    {
        $client = static::createClient();
        $client->request('GET', $client->getContainer()->get('router')->generate('endroid_qrcode_twig_functions'));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
