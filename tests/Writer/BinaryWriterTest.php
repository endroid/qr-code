<?php
/**
 * @author Eddy Pouw <eddypouw@gmail.com>
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCode;
use PHPUnit\Framework\TestCase;

class BinaryWriterTest extends TestCase
{
    /**
     * @var BinaryWriter
     */
    private $writer;

    protected function setUp()
    {
        $this->writer = new BinaryWriter();
    }

    public function testWriteStringConsecutiveCalls()
    {
        $qr_code = new QrCode('Boney M');
        self::assertSame(
            file_get_contents(__DIR__ . '/expected/expected_output_boney_m.txt'),
            $this->writer->writeString($qr_code)
        );

        $qr_code = new QrCode('Daddy Cool');
        self::assertSame(
            file_get_contents(__DIR__ . '/expected/expected_output_daddy_cool.txt'),
            $this->writer->writeString($qr_code)
        );
    }
}
