<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;

final class SvgWriter implements WriterInterface, LogoWriterInterface
{
    /** @param array<mixed> $options */
    public function writeQrCode(QrCodeInterface $qrCode, array $options = []): ResultInterface
    {
        $this->options = $options;

        if (!isset($this->options['block_id'])) {
            $this->options['block_id'] = 'block';
        }

        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $xml = new \SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
        $xml->addAttribute('version', '1.1');
        $xml->addAttribute('width', $matrix->getOuterSize().'px');
        $xml->addAttribute('height', $matrix->getOuterSize().'px');
        $xml->addAttribute('viewBox', '0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize());
        $xml->addChild('defs');

        $blockId = isset($options['block_id']) && $options['block_id'] ? $options['rect_id'] : 'block';
        $blockDefinition = $xml->defs->addChild('rect');
        $blockDefinition->addAttribute('id', $blockId);
        $blockDefinition->addAttribute('width', strval($matrix->getBlockSize()));
        $blockDefinition->addAttribute('height', strval($matrix->getBlockSize()));
        $blockDefinition->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()->getRed(), $qrCode->getForegroundColor()->getGreen(), $qrCode->getForegroundColor()->getBlue()));
        $blockDefinition->addAttribute('fill-opacity', strval($qrCode->getForegroundColor()->getOpacity()));

        $background = $xml->addChild('rect');
        $background->addAttribute('x', '0');
        $background->addAttribute('y', '0');
        $background->addAttribute('width', strval($matrix->getOuterSize()));
        $background->addAttribute('height', strval($matrix->getOuterSize()));
        $background->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()->getRed(), $qrCode->getBackgroundColor()->getGreen(), $qrCode->getBackgroundColor()->getBlue()));
        $background->addAttribute('fill-opacity', strval($qrCode->getBackgroundColor()->getOpacity()));

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    $block = $xml->addChild('use');
                    $block->addAttribute('x', strval($matrix->getMarginLeft() + $matrix->getBlockSize() * $columnIndex));
                    $block->addAttribute('y', strval($matrix->getMarginLeft() + $matrix->getBlockSize() * $rowIndex));
                    $block->addAttribute('xlink:href', '#'.$blockId, 'http://www.w3.org/1999/xlink');
                }
            }
        }

        return new SvgResult($xml);
    }

    public function writeLogo(LogoInterface $logo, ResultInterface $result): ResultInterface
    {
        if (!$result instanceof SvgResult) {
            throw new \Exception('Unable to write logo: instance of SvgResult expected');
        }

        return $result;
    }
}
