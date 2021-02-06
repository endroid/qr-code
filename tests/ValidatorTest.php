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
use Endroid\QrCode\Writer\Result\BinaryResult;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\Result\DebugResult;
use Endroid\QrCode\Writer\DebugWriter;
use Endroid\QrCode\Writer\Result\EpsResult;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\LabelWriterInterface;
use Endroid\QrCode\Writer\LogoWriterInterface;
use Endroid\QrCode\Writer\Result\PngResult;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\SvgResult;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\ValidatingWriterInterface;
use Endroid\QrCode\Writer\WriterInterface;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    /**
     * @testdox Can write $name and successfully validate result
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
