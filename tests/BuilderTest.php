<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\GdWriter;
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
            ->writer(new GdWriter())
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

    /**
     * @testdox Github issue #365
     */
    public function testGithubIssue365(): void
    {
        $data = 'Ahmad';
        $label = '';
        $size = 120;

        $result = Builder::create()
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($size)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->labelText($label)
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->build();

        $this->assertInstanceOf(PngResult::class, $result);
        $this->assertEquals('image/png', $result->getMimeType());

        $writer = new GdWriter();
        $qrCode = QrCode::create('https://xxxxxxx.xxxx/xxxxxxx')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(1200)
            ->setMargin(40)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255, 127));

        $result = $writer->write($qrCode);

        $this->assertInstanceOf(PngResult::class, $result);
        $this->assertEquals('image/png', $result->getMimeType());
    }
}
