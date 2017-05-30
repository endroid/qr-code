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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * QR code controller.
 */
class QrCodeController extends Controller
{
    /**
     * @Route("/{text}.{extension}", name="endroid_qrcode_generate", requirements={"text"="[\w\W]+"})
     *
     * @param Request $request
     * @param string $text
     * @param string $extension
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
     * @Route("/twig", name="endroid_qrcode_twig_functions")
     * @Template()
     *
     * @return array
     */
    public function twigFunctionsAction()
    {
        return [
            'message' => 'QR Code'
        ];
    }

    /**
     * @return QrCodeFactory
     */
    protected function getQrCodeFactory()
    {
        return $this->get('endroid.qrcode.factory');
    }
}
