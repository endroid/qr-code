<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\Result\PngResult;
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
