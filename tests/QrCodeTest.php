<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\BinaryResult;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\DebugResult;
use Endroid\QrCode\Writer\DebugWriter;
use Endroid\QrCode\Writer\EpsResult;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\PngResult;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgResult;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\ValidatingWriterInterface;
use Endroid\QrCode\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

final class QrCodeTest extends TestCase
{
    /**
     * @testdox Write as $resultClass with content type $contentType
     * @dataProvider writerProvider
     */
    public function testQrCode(WriterInterface $writer, string $resultClass, string $contentType): void
    {
        $qrCode = QrCode::create('Data')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create(__DIR__.'/assets/symfony.png')
            ->setResizeToWidth(50);

        // Create generic label
        $label = Label::create('Label')
            ->setTextColor(new Color(255, 0, 0))
            ->setBackgroundColor(new Color(0, 0, 0));

        $result = $writer->writeQrCode($qrCode);

        if ($writer instanceof LogoWriterInterface) {
            $result = $writer->writeLogo($logo, $result);
        }

        if ($writer instanceof LabelWriterInterface) {
            $result = $writer->writeLabel($label, $result);
        }

        if ($writer instanceof ValidatingWriterInterface) {
            if (PHP_VERSION_ID >= 80000) {
                $this->expectException(\Exception::class);
            }
            $writer->validateResult($result, $qrCode->getData());
        }

        $this->assertInstanceOf($resultClass, $result);
        $this->assertEquals($contentType, $result->getMimeType());
        $this->assertStringContainsString('data:'.$result->getMimeType().';base64,', $result->getDataUri());
    }

    public function writerProvider(): iterable
    {
        yield [new BinaryWriter(), BinaryResult::class, 'text/plain'];
        yield [new DebugWriter(), DebugResult::class, 'text/plain'];
        yield [new EpsWriter(), EpsResult::class, 'image/eps'];
//        yield [new PdfWriter(), PdfResult::class, ''];
        yield [new PngWriter(), PngResult::class, 'image/png'];
        yield [new SvgWriter(), SvgResult::class, 'image/svg+xml'];
    }

    /**
     * @testdox Write advanced example via builder
     */
    public function testBuilder(): void
    {
        $result = Builder::create()
            ->data('Custom QR code contents')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->logoPath(__DIR__.'/assets/symfony.png')
            ->labelText('This is the label')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->build()
        ;

        $this->assertInstanceOf(PngResult::class, $result);
        $this->assertEquals('image/png', $result->getMimeType());
    }

    /**
     * @testdox Can write $name
     * @dataProvider dataProvider
     */
    public function testReadability(string $name, string $data): void
    {
        if (PHP_VERSION_ID >= 80000) {
            $this->expectException(\Exception::class);
        }

        $result = Builder::create()
            ->data($data)
            ->validateResult(true)
            ->build()
        ;

        $this->assertInstanceOf(PngResult::class, $result);
        $this->assertEquals('image/png', $result->getMimeType());
    }

    public function dataProvider(): iterable
    {
        yield ['small data', 'Tiny'];
        yield ['data containing spaces', 'This one has spaces'];
        yield ['a large random character string', 'd2llMS9uU01BVmlvalM2YU9BUFBPTTdQMmJabHpqdndt'];
        yield ['a URL containing query parameters', 'https://this.is.an/url?with=query&string=attached'];
        yield ['a long number', '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111'];
        yield ['serialized data', '{"i":"serialized.data","v":1,"t":1,"d":"4AEPc9XuIQ0OjsZoSRWp9DRWlN6UyDvuMlyOYy8XjOw="}'];
        yield ['special characters', 'Spëci&al ch@ract3rs'];
        yield ['chinese characters', '有限公司'];
    }
}
