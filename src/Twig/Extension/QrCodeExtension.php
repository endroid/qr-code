<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Twig\Extension;

use Endroid\QrCode\Factory\QrCodeFactory;
use Endroid\QrCode\Writer\DataUriWriter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class QrCodeExtension extends Twig_Extension
{
    /**
     * @var QrCodeFactory
     */
    private $qrCodeFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param QrCodeFactory $qrCodeFactory
     * @param RouterInterface $router
     */
    public function __construct(QrCodeFactory $qrCodeFactory, RouterInterface $router)
    {
        $this->qrCodeFactory = $qrCodeFactory;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('qrcode_path', [$this, 'qrCodePathFunction']),
            new Twig_SimpleFunction('qrcode_url', [$this, 'qrCodeUrlFunction']),
            new Twig_SimpleFunction('qrcode_data_uri', [$this, 'qrCodeDataUriFunction']),
        ];
    }

    /**
     * @param string $text
     * @param array  $options
     *
     * @return string
     */
    public function qrcodeUrlFunction($text, array $options = [])
    {
        return $this->getQrCodeReference($text, $options, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param string $text
     * @param array  $options
     * @return string
     */
    public function qrCodePathFunction($text, array $options = [])
    {
        return $this->getQrCodeReference($text, $options, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * @param string $text
     * @param array $options
     * @param int $referenceType
     * @return string
     */
    public function getQrCodeReference($text, array $options = [], $referenceType)
    {
        $options['text'] = $text;

        if (!isset($options['extension'])) {
            $options['extension'] = 'png';
        }

        return $this->router->generate('endroid_qrcode', $options, $referenceType);
    }

    /**
     * @param string $text
     * @param array  $options
     * @return string
     */
    public function qrcodeDataUriFunction($text, array $options = [])
    {
        $options['text'] = $text;
        $qrCode = $this->qrCodeFactory->create($options);

        return $qrCode->writeString(DataUriWriter::class);
    }
}
