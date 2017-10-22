<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Bundle\QrCodeBundle\Controller;

use Endroid\QrCode\Factory\QrCodeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * QR code controller.
 */
class QrCodeController extends Controller
{
    /**
     * @param Request $request
     * @param string  $text
     * @param string  $extension
     *
     * @return Response
     */
    public function generateAction(Request $request, $text, $extension)
    {
        $options = $request->query->all();

        $qrCode = $this->getQrCodeFactory()->create($text, $options);
        $qrCode->setWriterByExtension($extension);

        return new Response($qrCode->writeString(), Response::HTTP_OK, ['Content-Type' => $qrCode->getContentType()]);
    }

    /**
     * @return Response
     */
    public function twigFunctionsAction()
    {
        if (!$this->has('twig')) {
            throw new \LogicException('You can not use the "@Template" annotation if the Twig Bundle is not available.');
        }

        $param = [
            'message' => 'QR Code',
        ];

        $renderedView = $this->get('twig')->render('@EndroidQrCode/QrCode/twigFunctions.html.twig', $param);

        return new Response($renderedView, Response::HTTP_OK);
    }

    /**
     * @return QrCodeFactory
     */
    protected function getQrCodeFactory()
    {
        return $this->get('endroid.qrcode.factory');
    }
}
