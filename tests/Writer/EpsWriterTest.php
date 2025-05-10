<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests\Writer;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Logo\Logo;
use PHPUnit\Framework\TestCase;

final class EpsWriterTest extends TestCase
{
    public function testWriteWithLogo(): void
    {
        $qrCode = new QrCode('QR Code with logo');
        $logo = new Logo(__DIR__.'/../assets/symfony.svg', 50, 50);

        $writer = new EpsWriter();
        $result = $writer->write($qrCode, $logo);

        $this->assertStringContainsString('gsave', $result->getString());
        $this->assertStringContainsString('translate', $result->getString());
        $this->assertStringContainsString('scale', $result->getString());
        $this->assertStringContainsString('run', $result->getString());
    }
}
