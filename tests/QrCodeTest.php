<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Model\ErrorCorrectionLevel;
use Endroid\QrCode\Model\Label;
use Endroid\QrCode\Model\Logo;
use Endroid\QrCode\Model\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

class QrCodeTest extends TestCase
{
    /**
     * @testdox The QR code can be created
     */
    public function testQrCode(): void
    {
        $qrCode = QrCode::create('text');
        dump($qrCode);
        die;



        $logo = new Logo(__DIR__.'/../assets/symfony.png');
        $label = new Label('label');

        $writer = new PngWriter($qrCode, $logo, $label);
    }

}
