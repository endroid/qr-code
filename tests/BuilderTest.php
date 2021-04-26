<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\PngResult;
use Endroid\QrCode\Writer\Result\SvgResult;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

final class BuilderTest extends TestCase
{
    /**
     * @testdox Write advanced example via builder
     * @dataProvider imagesDataProvider
     */
    public function testBuilder(WriterInterface $writer, string $logoPath, string $resultClass, string $mimeType): void
    {
        $result = Builder::create()
            ->writer($writer)
            ->writerOptions([])
            ->data('Custom QR code contents')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->logoPath($logoPath)
            ->labelText('This is the label')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->build()
        ;

        $this->assertInstanceOf($resultClass, $result);
        $this->assertEquals($mimeType, $result->getMimeType());
    }

    public function imagesDataProvider(): array
    {
        return [
            'png qr, png logo' => [
                new PngWriter(),
                __DIR__.'/assets/symfony.png',
                PngResult::class,
                'image/png',
            ],
            'png qr, svg logo' => [
                new PngWriter(),
                __DIR__.'/assets/symfony.svg',
                PngResult::class,
                'image/png',
            ],
            'svg qr, svg logo' => [
                new SvgWriter(),
                __DIR__.'/assets/symfony.svg',
                SvgResult::class,
                'image/svg+xml',
            ],
            'svg qr, png logo' => [
                new SvgWriter(),
                __DIR__.'/assets/symfony.png',
                SvgResult::class,
                'image/svg+xml',
            ]
        ];
    }
}
