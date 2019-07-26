<?php

namespace Endroid\QrCode\Tests\Writer;

use Color\Value\CMYK;
use Color\Value\RGB;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\EpsWriter;
use PHPUnit\Framework\TestCase;

/**
 * Class EpsWriterTest
 * 
 * @package Endroid\QrCode\Tests\Writer
 */
class EpsWriterTest extends TestCase
{
    /**
     * Tests if RGB colors can be added successfully
     */
    public function testCanHandleRGBColors()
    {
        $backgroundColor = [0, 4, 4];
        $foregroundColor = [0, 255, 200];

        $qrCode = new QrCode();
        $qrCode->setWriter(new EpsWriter());
        $qrCode
            ->setBackgroundColor(
                new RGB(...$backgroundColor)
            )
            ->setForegroundColor(
                new RGB(...$foregroundColor)
            )
        ;

        $string = $qrCode->writeString();
        $colorLines = [];

        foreach (preg_split("/((\r?\n)|(\r\n?))/", $string) as $line){
            if (strpos($line, 'setrgbcolor') !== false) {
                $colorLines[] = $line;
            }
        }

        $this->assertEquals(
            $colorLines[0] ?? '',
            implode(' ', $backgroundColor).' setrgbcolor'
        );

        $this->assertEquals(
            $colorLines[1] ?? '',
            implode(' ', $foregroundColor).' setrgbcolor'
        );
    }
    
    /**
     * Tests if CMYK colors can be added successfully
     */
    public function testCanHandleCMYKColors()
    {
        $backgroundColor = [0, 5, 5, 0];
        $foregroundColor = [0, 100, 100, 0];

        $qrCode = new QrCode();
        $qrCode->setWriter(new EpsWriter());
        $qrCode
            ->setBackgroundColor(
                new CMYK(...$backgroundColor)
            )
            ->setForegroundColor(
                new CMYK(...$foregroundColor)
            )
        ;

        $string = $qrCode->writeString();
        $colorLines = [];

        foreach (preg_split("/((\r?\n)|(\r\n?))/", $string) as $line){
            if (strpos($line, 'setcmykcolor') !== false) {
                $colorLines[] = $line;
            }
        }

        $this->assertEquals(
            $colorLines[0],
            implode(' ', $backgroundColor).' setcmykcolor'
        );

        $this->assertEquals(
            $colorLines[1],
            implode(' ', $foregroundColor).' setcmykcolor'
        );
    }
}