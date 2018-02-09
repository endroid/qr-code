<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Twig\Extension;

use Endroid\QrCode\Factory\QrCodeFactoryInterface;
use Twig_Extension;
use Twig_SimpleFunction;

final class QrCodeExtension extends Twig_Extension
{
    private $qrCodeFactory;

    public function __construct(QrCodeFactoryInterface $qrCodeFactory)
    {
        $this->qrCodeFactory = $qrCodeFactory;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('qrcode_data_uri', [$this, 'qrCodeDataUriFunction']),
        ];
    }

    public function qrCodeDataUriFunction(string $text, array $options = []): string
    {
        return $this->qrCodeFactory->create($text, $options)->writeDataUri();
    }
}
