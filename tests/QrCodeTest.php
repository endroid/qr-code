<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Alignment\Center;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\LabelBuilder;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Logo\LogoBuilder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\High;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\QrCodeBuilder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ResultInterface;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

final class QrCodeTest extends TestCase
{
    /**
     * @testdox The QR code can be written using all packed writers
     */
    public function testQrCode(): void
    {
        // Create generic QR Code
        $qrCode = QrCode::create('Data')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(100, 100, 100))
            ->setBackgroundColor(new Color(255, 0, 255))
        ;

        $this->assertInstanceOf(QrCode::class, $qrCode);

        // Create generic logo
        $logo = Logo::create(__DIR__.'/assets/symfony.png');

        $this->assertInstanceOf(Logo::class, $logo);

        // Create generic label
        $label = Label::create('Label');

        $this->assertInstanceOf(Label::class, $label);

        foreach ($this->getWriters() as $writer) {
            $result = $writer->writeQrCode($qrCode);
            if ($writer instanceof LogoWriterInterface) {
//                $result = $writer->writeLogo($logo);
            }
            if ($writer instanceof LabelWriterInterface) {
//                $result = $writer->writeLabel($label);
            }
            $this->assertInstanceOf(ResultInterface::class, $result);
            $result->getString();
        }



//        $result = Builder::create()
//            ->data('data')
//            ->encoding(new Encoding('UTF-8'))
//            ->errorCorrectionLevel(new High())
//            ->writer(new PngWriter())
//            ->logoPath()
//            ->logoResizeWidth(100)
//            ->labelText('My fancy label')
//            ->labelAlignment(new Center())
//            ->labelMargin(new Margin(5, 5, 5, 5))
//            ->build();
//
//        $this->assertInstanceOf(ResultInterface::class, $result);
    }

    /** @return WriterInterface[] */
    private function getWriters(): iterable
    {
//        yield new BinaryWriter();
        yield new PngWriter();
    }
}
