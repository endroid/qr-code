<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeEnlarge;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeInterface;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeShrink;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\DebugWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\PdfWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\BinaryResult;
use Endroid\QrCode\Writer\Result\DebugResult;
use Endroid\QrCode\Writer\Result\EpsResult;
use Endroid\QrCode\Writer\Result\PdfResult;
use Endroid\QrCode\Writer\Result\PngResult;
use Endroid\QrCode\Writer\Result\SvgResult;
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
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);

        if ($writer instanceof ValidatingWriterInterface) {
            if ($writer instanceof PngWriter && PHP_VERSION_ID >= 80000) {
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
        yield [new PdfWriter(), PdfResult::class, 'application/pdf'];
        yield [new PngWriter(), PngResult::class, 'image/png'];
        yield [new SvgWriter(), SvgResult::class, 'image/svg+xml'];
    }

    /**
     * @testdox Size and margin are handled correctly
     */
    public function testSetSize(): void
    {
        $imageData = Builder::create()
            ->data('QR Code')
            ->size(400)
            ->margin(15)
            ->build()->getString();

        $image = imagecreatefromstring($imageData);

        $this->assertTrue(430 === imagesx($image));
        $this->assertTrue(430 === imagesy($image));
    }

    /**
     * @testdox Size and margin are handled correctly with rounded blocks
     * @dataProvider roundedSizeProvider
     */
    public function testSetSizeRounded(int $size, int $margin, RoundBlockSizeModeInterface $roundBlockSizeMode, int $expectedSize): void
    {
        $imageData = Builder::create()
            ->data('QR Code contents with some length to have some data')
            ->size($size)
            ->margin($margin)
            ->roundBlockSizeMode($roundBlockSizeMode)
            ->build()->getString();

        $image = imagecreatefromstring($imageData);

        $this->assertTrue(imagesx($image) === $expectedSize);
        $this->assertTrue(imagesy($image) === $expectedSize);
    }

    public function roundedSizeProvider()
    {
        yield [400, 0, new RoundBlockSizeModeEnlarge(), 406];
        yield [400, 5, new RoundBlockSizeModeEnlarge(), 416];
        yield [400, 0, new RoundBlockSizeModeMargin(), 400];
        yield [400, 5, new RoundBlockSizeModeMargin(), 410];
        yield [400, 0, new RoundBlockSizeModeShrink(), 377];
        yield [400, 5, new RoundBlockSizeModeShrink(), 387];
    }

    /**
     * @testdox Invalid logo path results in exception
     */
    public function testInvalidLogoPath(): void
    {
        $writer = new SvgWriter();
        $qrCode = QrCode::create('QR Code');

        $logo = Logo::create('/my/invalid/path.png');
        $this->expectExceptionMessage('Invalid data at path "/my/invalid/path.png"');
        $writer->write($qrCode, $logo);
    }

    /**
     * @testdox Invalid logo data results in exception
     */
    public function testInvalidLogoData(): void
    {
        $writer = new SvgWriter();
        $qrCode = QrCode::create('QR Code');

        $logo = Logo::create(__DIR__.'/QrCodeTest.php');
        $this->expectExceptionMessage('Logo path is not an image');
        $writer->write($qrCode, $logo);
    }

    /**
     * @testdox Result can be saved to file
     */
    public function testSaveToFile(): void
    {
        $path = __DIR__.'/test-save-to-file.png';

        $writer = new PngWriter();
        $qrCode = new QrCode('QR Code');
        $writer->write($qrCode)->saveToFile($path);

        $image = imagecreatefromstring(file_get_contents($path));

        $this->assertTrue(false !== $image);

        if (PHP_VERSION_ID < 80000) {
            imagedestroy($image);
        }

        unlink($path);
    }

    /**
     * @testdox Line breaks are not supported
     */
    public function testLabelLineBreaks(): void
    {
        $qrCode = QrCode::create('QR Code');
        $label = Label::create("this\none has\nline breaks in it");

        $writer = new PngWriter();
        $this->expectExceptionMessage('Label does not support line breaks');
        $writer->write($qrCode, null, $label);
    }

    /**
     * @testdox Block size should be at least 1
     */
    public function testBlockSizeTooSmall(): void
    {
        $aLotOfData = str_repeat('alot', 100);
        $qrCode = QrCode::create($aLotOfData)
            ->setSize(10);

        $matrixFactory = new MatrixFactory();
        $this->expectExceptionMessage('Too much data: increase image dimensions or lower error correction level');
        $matrixFactory->create($qrCode);
    }

    /**
     * @testdox PNG Writer does not accept SVG logo, while SVG writer does
     */
    public function testSvgLogo(): void
    {
        $qrCode = QrCode::create('QR Code');
        $logo = Logo::create(__DIR__.'/assets/symfony.svg')
            ->setResizeToWidth(100)
            ->setResizeToHeight(50)
        ;

        $svgWriter = new SvgWriter();
        $result = $svgWriter->write($qrCode, $logo);
        $this->assertInstanceOf(SvgResult::class, $result);

        $pngWriter = new PngWriter();
        $this->expectExceptionMessage('PNG Writer does not support SVG logo');
        $pngWriter->write($qrCode, $logo);
    }
}
