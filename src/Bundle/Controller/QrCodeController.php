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
     * @Route("/{text}.{extension}", name="endroid_qrcode", requirements={"text"="[\w\W]+", "extension"="png|svg"})
     */
    public function generateAction(Request $request, $text, $extension)
    {
        $options = $request->query->all();

        $qrCode = $this->getQrCodeFactory()->createQrCode($options);
        $qrCode->setText($text);

        switch ($extension) {
            case 'png':
                $data = $qrCode->getPngData();
                $contentType = 'image/png';
                break;
            case 'svg':
                $data = $qrCode->getSvgData();
                $contentType = 'image/svg+xml';
                break;
        }

        return new Response($data, 200, ['Content-Type' => $contentType]);
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
