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
     * @Route("/{text}.{extension}", name="endroid_qrcode", requirements={"text"="[\w\W]+"})
     *
     * @param Request $request
     * @param string $text
     * @param string $extension
     * @return Response
     */
    public function generateAction(Request $request, $text, $extension)
    {
        $options = $request->query->all();
        $options['text'] = $text;

        $qrCode = $this->getQrCodeFactory()->create($options);
        $writer = $qrCode->getWriterByExtension($extension);

        return new Response(
            $writer->writeString(),
            Response::HTTP_OK,
            ['Content-Type' => $writer->getContentType()]
        );
    }

    /**
     * @return QrCodeFactory
     */
    protected function getQrCodeFactory()
    {
        return $this->get('endroid.qrcode.factory');
    }
}
