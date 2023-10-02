<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
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
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->logoPath(__DIR__.'/assets/symfony.png')
            ->labelText('This is the label')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
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
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($size)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->labelText($label)
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
            ->build();

        $this->assertInstanceOf(PngResult::class, $result);
        $this->assertEquals('image/png', $result->getMimeType());

        $writer = new PngWriter();
        $qrCode = QrCode::create('https://xxxxxxx.xxxx/xxxxxxx')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(1200)
            ->setMargin(40)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255, 127));

        $result = $writer->write($qrCode);

        $this->assertInstanceOf(PngResult::class, $result);
        $this->assertEquals('image/png', $result->getMimeType());
    }
}
