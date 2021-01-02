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
use Endroid\QrCode\Writer\BinaryResult;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\PngResult;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ResultInterface;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\WriterInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use PHPUnit\Framework\TestCase;

final class QrCodeTest extends TestCase
{
    /**
     * @testdox Write as $resultClass with content type $contentType
     * @dataProvider getWriters()
     */
    public function testQrCode(WriterInterface $writer, string $resultClass, string $contentType): void
    {
        $qrCode = QrCode::create('Data')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255))
        ;

        // Create generic logo
        $logo = Logo::create(__DIR__.'/assets/symfony.png')
            ->setResizeToWidth(200)
        ;

        // Create generic label
        $label = Label::create('Label')
            ->setTextColor(new Color(255, 0, 0))
            ->setBackgroundColor(new Color(0, 0, 0))
        ;

        $result = $writer->writeQrCode($qrCode);
        if ($writer instanceof LogoWriterInterface) {
            $result = $writer->writeLogo($logo, $result);
        }
        if ($writer instanceof LabelWriterInterface) {
            $result = $writer->writeLabel($label, $result);
        }

        $this->assertInstanceOf($resultClass, $result);
        $this->assertEquals($contentType, $result->getMimeType());
    }

    public function getWriters(): iterable
    {
        yield [new BinaryWriter(), BinaryResult::class, 'text/plain'];
        yield [new PngWriter(), PngResult::class, 'image/png'];
    }
}
