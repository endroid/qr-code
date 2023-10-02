<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\ConsoleWriter;
use Endroid\QrCode\Writer\DebugWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\GifWriter;
use Endroid\QrCode\Writer\PdfWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\BinaryResult;
use Endroid\QrCode\Writer\Result\ConsoleResult;
use Endroid\QrCode\Writer\Result\DebugResult;
use Endroid\QrCode\Writer\Result\EpsResult;
use Endroid\QrCode\Writer\Result\GifResult;
use Endroid\QrCode\Writer\Result\PdfResult;
use Endroid\QrCode\Writer\Result\PngResult;
use Endroid\QrCode\Writer\Result\SvgResult;
use Endroid\QrCode\Writer\Result\WebpResult;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\ValidatingWriterInterface;
use Endroid\QrCode\Writer\WebPWriter;
use Endroid\QrCode\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

final class QrCodeTest extends TestCase
{
    /**
     * @testdox Write as $resultClass with content type $contentType
     *
     * @dataProvider writerProvider
     */
    public function testQrCode(WriterInterface $writer, string $resultClass, string $contentType): void
    {
        $qrCode = QrCode::create('Data')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create(__DIR__.'/assets/symfony.png')
            ->setResizeToWidth(50);

        // Create generic label
        $label = Label::create('Label')
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);
        $this->assertInstanceOf(MatrixInterface::class, $result->getMatrix());

        if ($writer instanceof ValidatingWriterInterface) {
            $writer->validateResult($result, $qrCode->getData());
        }

        $this->assertInstanceOf($resultClass, $result);
        $this->assertEquals($contentType, $result->getMimeType());
        $this->assertStringContainsString('data:'.$result->getMimeType().';base64,', $result->getDataUri());
    }

    public static function writerProvider(): iterable
    {
        yield [new BinaryWriter(), BinaryResult::class, 'text/plain'];
        yield [new ConsoleWriter(), ConsoleResult::class, 'text/plain'];
        yield [new DebugWriter(), DebugResult::class, 'text/plain'];
        yield [new EpsWriter(), EpsResult::class, 'image/eps'];
        yield [new GifWriter(), GifResult::class, 'image/gif'];
        yield [new PdfWriter(), PdfResult::class, 'application/pdf'];
        yield [new PngWriter(), PngResult::class, 'image/png'];
        yield [new SvgWriter(), SvgResult::class, 'image/svg+xml'];
        yield [new WebPWriter(), WebpResult::class, 'image/webp'];
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
     *
     * @dataProvider roundedSizeProvider
     */
    public function testSetSizeRounded(int $size, int $margin, RoundBlockSizeMode $roundBlockSizeMode, int $expectedSize): void
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

    public static function roundedSizeProvider(): iterable
    {
        yield [400, 0, RoundBlockSizeMode::Enlarge, 406];
        yield [400, 5, RoundBlockSizeMode::Enlarge, 416];
        yield [400, 0, RoundBlockSizeMode::Margin, 400];
        yield [400, 5, RoundBlockSizeMode::Margin, 410];
        yield [400, 0, RoundBlockSizeMode::Shrink, 377];
        yield [400, 5, RoundBlockSizeMode::Shrink, 387];
    }

    /**
     * @testdox Invalid logo path results in exception
     */
    public function testInvalidLogoPath(): void
    {
        $writer = new SvgWriter();
        $qrCode = QrCode::create('QR Code');

        $logo = Logo::create('/my/invalid/path.png');
        $this->expectExceptionMessageMatches('#Could not read logo image data from path "/my/invalid/path.png"#');
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
