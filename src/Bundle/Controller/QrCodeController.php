<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Bundle\Controller;

use Endroid\QrCode\Factory\QrCodeFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * QR code controller.
 */
class QrCodeController extends Controller
{
    /**
     * @Route("/{text}.{extension}", name="endroid_qrcode", requirements={"text"="[\w\W]+", "extension"="jpg|png|gif"})
     */
    public function generateAction(Request $request, $text, $extension)
    {
        $options = $request->query->all();

        $qrCode = $this->getQrCodeFactory()->createQrCode($options);
        $qrCode->setText($text);

        $mime_type = 'image/'.$extension;
        if ($extension == 'jpg') {
            $mime_type = 'image/jpeg';
        }

        return new Response($qrCode->get($extension), 200, ['Content-Type' => $mime_type]);
    }

    /**
     * Returns the QR code factory.
     *
     * @return QrCodeFactory
     */
    protected function getQrCodeFactory()
    {
        return $this->get('endroid.qrcode.factory');
    }
}
