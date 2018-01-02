<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Twig\Extension;

use Endroid\QrCode\Factory\QrCodeFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * @deprecated as of 3.1 and will be removed in 4.0. Use the QrCodeRoutingExtension and QrCodeUriExtension instead.
 */
class QrCodeExtension extends Twig_Extension
{
    private $qrCodeFactory;
    private $router;

    public function __construct(QrCodeFactoryInterface $qrCodeFactory, RouterInterface $router)
    {
        $this->qrCodeFactory = $qrCodeFactory;
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('qr_code_path', [$this, 'qrCodePathFunction']),
            new Twig_SimpleFunction('qr_code_url', [$this, 'qrCodeUrlFunction']),
            new Twig_SimpleFunction('qr_code_data_uri', [$this, 'qrCodeDataUriFunction']),
        ];
    }

    public function qrCodeUrlFunction(string $text, array $options = []): string
    {
        @trigger_error(sprintf('Using %s is deprecated as of 3.1 and will be removed in 4.0. Install the EndroidQrCodeBundle instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->getQrCodeReference($text, $options, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function qrCodePathFunction(string $text, array $options = []): string
    {
        @trigger_error(sprintf('Using %s is deprecated as of 3.1 and will be removed in 4.0. Install the EndroidQrCodeBundle instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->getQrCodeReference($text, $options, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function getQrCodeReference(string $text, array $options = [], int $referenceType): string
    {
        $qrCode = $this->qrCodeFactory->create($text, $options);
        $supportedExtensions = $qrCode->getWriter()->getSupportedExtensions();

        $options['text'] = $text;
        $options['extension'] = current($supportedExtensions);

        return $this->router->generate('endroid_qr_code_generate', $options, $referenceType);
    }

    public function qrCodeDataUriFunction(string $text, array $options = []): string
    {
        @trigger_error(sprintf('Using %s is deprecated as of 3.1 and will be removed in 4.0. Use %s::qrCodeDataUriFunction() instead.', __METHOD__, QrCodeUriExtension::class), E_USER_DEPRECATED);

        $qrCode = $this->qrCodeFactory->create($text, $options);

        return $qrCode->writeDataUri();
    }
}
