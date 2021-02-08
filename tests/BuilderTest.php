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
use PHPUnit\Framework\TestCase;

final class BuilderTest extends TestCase
{
    /**
     * @testdox Write advanced example via builder
     */
    public function testBuilder(): void
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
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
}
