<?php

namespace Endroid\QrCode\Tests\Writer;

use Color\Value\RGB;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use PHPUnit\Framework\TestCase;

/**
 * Class SvgWriterTest
 * 
 * @package Endroid\QrCode\Tests\Writer
 */
class SvgWriterTest extends TestCase
{
    /**
     * Tests if HEX colors can be added successfully
     */
    public function testCanHandleHEXColors()
    {
        $backgroundColor = [0, 4, 4];
        $foregroundColor = [0, 255, 200];

        $backgroundColorRGB = new RGB(...$backgroundColor);
        $foregroundColorRGB = new RGB(...$foregroundColor);
        
        $qrCode = new QrCode();
        $qrCode->setWriter(new SvgWriter());
        $qrCode
            ->setBackgroundColor($backgroundColorRGB)
            ->setForegroundColor($foregroundColorRGB)
        ;

        $string = $qrCode->writeString();

        $this->assertNotFalse(
            strpos($string, 'fill="'.$foregroundColorRGB->getHEX().'"'),
            'No hex color found'
        );

        $this->assertNotFalse(
            strpos($string, 'fill="'.$backgroundColorRGB->getHEX().'"'),
            'No hex color found'
        );
    }
}