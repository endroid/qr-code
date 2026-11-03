<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\PdfWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WebPWriter;
use PHPUnit\Framework\TestCase;

final class WriterRenderingTest extends TestCase
{
    public function testCompactSvgClosesAndRoundsHorizontalRuns(): void
    {
        $qrCode = new QrCode(
            data: 'QR Code',
            size: 301,
            margin: 11,
            roundBlockSizeMode: RoundBlockSizeMode::None,
            foregroundColor: new Color(23, 67, 109, 24)
        );

        $result = (new SvgWriter())->write($qrCode);
        $xml = new \SimpleXMLElement($result->getString());

        // The first finder-pattern run spans seven modules at fractional block size.
        $this->assertStringStartsWith('M11,11L111.33,11L111.33,25.33L11,25.33Z', (string) $xml->path['d']);
        $this->assertSame('#17436d', (string) $xml->path['fill']);
        $this->assertSame((string) $qrCode->getForegroundColor()->getOpacity(), (string) $xml->path['fill-opacity']);
    }

    public function testEpsPreservesColorFormattingAndCoordinatePrecision(): void
    {
        $qrCode = new QrCode(
            data: 'QR Code',
            size: 301,
            margin: 11,
            roundBlockSizeMode: RoundBlockSizeMode::None,
            foregroundColor: new Color(23, 67, 109),
            backgroundColor: new Color(237, 241, 249)
        );

        $lines = explode("\n", (new EpsWriter())->write($qrCode)->getString());

        $this->assertSame('2.37 2.41 2.49 setrgbcolor', $lines[3]);
        $this->assertSame('0.23 0.67 1.09 setrgbcolor', $lines[5]);
        $this->assertSame('11.0000000000 11.0000000000 14.3333333333 14.3333333333 F', $lines[6]);
    }

    public function testNullWriterOptionsRetainDefaults(): void
    {
        $qrCode = new QrCode('QR Code');
        $cases = [
            [new PngWriter(), ['compression_level' => null]],
            [new WebPWriter(), ['quality' => null]],
            [new SvgWriter(), ['compact' => null, 'block_id' => null, 'exclude_xml_declaration' => null, 'exclude_svg_width_and_height' => null]],
        ];

        foreach ($cases as [$writer, $options]) {
            $this->assertSame($writer->write($qrCode)->getString(), $writer->write($qrCode, options: $options)->getString());
        }
    }

    public function testExplicitNullPngPaletteOptionKeepsTrueColor(): void
    {
        $writer = new PngWriter();
        $qrCode = new QrCode('QR Code');
        $palette = imagecreatefromstring($writer->write($qrCode)->getString());
        $trueColor = imagecreatefromstring($writer->write($qrCode, options: ['number_of_colors' => null])->getString());

        $this->assertFalse(imageistruecolor($palette));
        $this->assertTrue(imageistruecolor($trueColor));
    }

    public function testPdfUsesExistingDocumentWithoutAddingPage(): void
    {
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetXY(5, 5);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(20, 5, 'Existing content');

        $result = (new PdfWriter())->write(new QrCode('QR Code'), options: [
            'fpdf' => $pdf,
            'unit' => null,
            'x' => 7,
            'y' => 11,
            'link' => 'https://example.com',
        ]);

        $this->assertSame($pdf, $result->getPdf());
        $this->assertSame(1, $pdf->PageNo());
        $this->assertStringContainsString('/URI (https://example.com)', $result->getString());
    }
}
