<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\ImageData\LogoImageData;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\Result\SvgResult;

final readonly class SvgWriter implements WriterInterface
{
    public const int DECIMAL_PRECISION = 2;
    public const string WRITER_OPTION_COMPACT = 'compact';
    public const string WRITER_OPTION_BLOCK_ID = 'block_id';
    public const string WRITER_OPTION_EXCLUDE_XML_DECLARATION = 'exclude_xml_declaration';
    public const string WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT = 'exclude_svg_width_and_height';
    public const string WRITER_OPTION_FORCE_XLINK_HREF = 'force_xlink_href';

    #[\Override]
    public function write(
        QrCodeInterface $qrCode,
        ?LogoInterface $logo = null,
        ?LabelInterface $label = null,
        array $options = [],
    ): ResultInterface {
        $options[self::WRITER_OPTION_COMPACT] ??= true;
        $options[self::WRITER_OPTION_BLOCK_ID] ??= 'block';
        $options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION] ??= false;
        $options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT] ??= false;

        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $xml = new \SimpleXMLElement(
            '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>',
        );
        $xml->addAttribute('version', '1.1');
        if (!$options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT]) {
            $xml->addAttribute('width', $matrix->getOuterSize() . 'px');
            $xml->addAttribute('height', $matrix->getOuterSize() . 'px');
        }
        $xml->addAttribute('viewBox', '0 0 ' . $matrix->getOuterSize() . ' ' . $matrix->getOuterSize());

        // These fixed, valid element names always produce a child element.
        /** @var \SimpleXMLElement $background */
        $background = $xml->addChild('rect');
        $background->addAttribute('x', '0');
        $background->addAttribute('y', '0');
        $background->addAttribute('width', strval($matrix->getOuterSize()));
        $background->addAttribute('height', strval($matrix->getOuterSize()));
        $background->addAttribute('fill', $this->formatColor($qrCode->getBackgroundColor()));
        $background->addAttribute('fill-opacity', strval($qrCode->getBackgroundColor()->getOpacity()));

        if ($options[self::WRITER_OPTION_COMPACT]) {
            $this->writePath($xml, $qrCode, $matrix);
        }
        if (!$options[self::WRITER_OPTION_COMPACT]) {
            $this->writeBlockDefinitions($xml, $qrCode, $matrix, $options);
        }

        $result = new SvgResult($matrix, $xml, boolval($options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION]));

        if ($logo instanceof LogoInterface) {
            $this->addLogo($logo, $result, $options);
        }

        return $result;
    }

    private function writePath(\SimpleXMLElement $xml, QrCodeInterface $qrCode, MatrixInterface $matrix): void
    {
        $path = '';
        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            $left = $matrix->getMarginLeft();
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 !== $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    continue;
                }

                // When we are at the first column or when the previous column was 0 set new left
                if (0 === $columnIndex || 0 === $matrix->getBlockValue($rowIndex, $columnIndex - 1)) {
                    $left = $matrix->getMarginLeft() + ($matrix->getBlockSize() * $columnIndex);
                }
                // Close the rectangle at the end of a run of dark modules.
                if (
                    $columnIndex === ($matrix->getBlockCount() - 1)
                    || 0 === $matrix->getBlockValue($rowIndex, $columnIndex + 1)
                ) {
                    $top = $matrix->getMarginLeft() + ($matrix->getBlockSize() * $rowIndex);
                    $bottom = $matrix->getMarginLeft() + ($matrix->getBlockSize() * ($rowIndex + 1));
                    $right = $matrix->getMarginLeft() + ($matrix->getBlockSize() * ($columnIndex + 1));
                    $path .= $this->createRectanglePath($left, $top, $right, $bottom);
                }
            }
        }

        /** @var \SimpleXMLElement $pathDefinition */
        $pathDefinition = $xml->addChild('path');
        $pathDefinition->addAttribute('fill', $this->formatColor($qrCode->getForegroundColor()));
        $pathDefinition->addAttribute('fill-opacity', strval($qrCode->getForegroundColor()->getOpacity()));
        $pathDefinition->addAttribute('d', $path);
    }

    /** @param array<string, mixed> $options */
    private function writeBlockDefinitions(
        \SimpleXMLElement $xml,
        QrCodeInterface $qrCode,
        MatrixInterface $matrix,
        array $options,
    ): void {
        /** @var \SimpleXMLElement $definitions */
        $definitions = $xml->addChild('defs');
        /** @var \SimpleXMLElement $blockDefinition */
        $blockDefinition = $definitions->addChild('rect');
        $blockDefinition->addAttribute('id', strval($options[self::WRITER_OPTION_BLOCK_ID] ?? 'block'));
        $blockDefinition->addAttribute('width', $this->formatNumber($matrix->getBlockSize()));
        $blockDefinition->addAttribute('height', $this->formatNumber($matrix->getBlockSize()));
        $blockDefinition->addAttribute('fill', $this->formatColor($qrCode->getForegroundColor()));
        $blockDefinition->addAttribute('fill-opacity', strval($qrCode->getForegroundColor()->getOpacity()));

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 !== $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    continue;
                }

                /** @var \SimpleXMLElement $block */
                $block = $xml->addChild('use');
                $block->addAttribute(
                    'x',
                    $this->formatNumber($matrix->getMarginLeft() + ($matrix->getBlockSize() * $columnIndex)),
                );
                $block->addAttribute(
                    'y',
                    $this->formatNumber($matrix->getMarginLeft() + ($matrix->getBlockSize() * $rowIndex)),
                );
                $block->addAttribute(
                    'xlink:href',
                    '#' . strval($options[self::WRITER_OPTION_BLOCK_ID] ?? 'block'),
                    'http://www.w3.org/1999/xlink',
                );
            }
        }
    }

    /** @param array<string, mixed> $options */
    private function addLogo(LogoInterface $logo, SvgResult $result, array $options): void
    {
        if ($logo->getPunchoutBackground()) {
            throw new \Exception('The SVG writer does not support logo punchout background');
        }

        $logoImageData = LogoImageData::createForLogo($logo);

        $options[self::WRITER_OPTION_FORCE_XLINK_HREF] ??= false;

        $xml = $result->getXml();

        /** @var \SimpleXMLElement $xmlAttributes */
        $xmlAttributes = $xml->attributes();

        $x = (intval($xmlAttributes['width']) / 2) - ($logoImageData->getWidth() / 2);
        $y = (intval($xmlAttributes['height']) / 2) - ($logoImageData->getHeight() / 2);

        /** @var \SimpleXMLElement $imageDefinition */
        $imageDefinition = $xml->addChild('image');
        $imageDefinition->addAttribute('x', strval($x));
        $imageDefinition->addAttribute('y', strval($y));
        $imageDefinition->addAttribute('width', strval($logoImageData->getWidth()));
        $imageDefinition->addAttribute('height', strval($logoImageData->getHeight()));
        $imageDefinition->addAttribute('preserveAspectRatio', 'none');

        if ($options[self::WRITER_OPTION_FORCE_XLINK_HREF]) {
            $imageDefinition->addAttribute(
                'xlink:href',
                $logoImageData->createDataUri(),
                'http://www.w3.org/1999/xlink',
            );
            return;
        }

        $imageDefinition->addAttribute('href', $logoImageData->createDataUri());
    }

    private function createRectanglePath(float $left, float $top, float $right, float $bottom): string
    {
        return (
            'M'
            . $this->formatNumber($left)
            . ','
            . $this->formatNumber($top)
            . 'L'
            . $this->formatNumber($right)
            . ','
            . $this->formatNumber($top)
            . 'L'
            . $this->formatNumber($right)
            . ','
            . $this->formatNumber($bottom)
            . 'L'
            . $this->formatNumber($left)
            . ','
            . $this->formatNumber($bottom)
            . 'Z'
        );
    }

    private function formatColor(ColorInterface $color): string
    {
        return sprintf('#%02x%02x%02x', $color->getRed(), $color->getGreen(), $color->getBlue());
    }

    private function formatNumber(float $number): string
    {
        $string = number_format($number, self::DECIMAL_PRECISION, decimal_separator: '.', thousands_separator: '');
        $string = rtrim($string, characters: '0');

        return rtrim($string, characters: '.');
    }
}
