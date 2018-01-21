<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;
use SimpleXMLElement;

class SvgWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        $data = $this->getData($qrCode);

        $svg = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>'
            .'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>'
        );
        $svg->addAttribute('version', '1.1');
        $svg->addAttribute('width', $data['inner_width'].'px');
        $svg->addAttribute('height', $data['inner_height'].'px');
        $svg->addAttribute('viewBox', '0 0 '.$data['outer_width'].' '.$data['outer_height']);
        $svg->addChild('defs');

        // Block definition
        $blockDefinition = $svg->defs->addChild('rect');
        $blockDefinition->addAttribute('id', 'block');
        $blockDefinition->addAttribute('width', $data['block_size']);
        $blockDefinition->addAttribute('height', $data['block_size']);
        $blockDefinition->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()['r'], $qrCode->getForegroundColor()['g'], $qrCode->getForegroundColor()['b']));
        $blockDefinition->addAttribute('fill-opacity', $this->getOpacity($qrCode->getForegroundColor()['a']));

        // Background
        $background = $svg->addChild('rect');
        $background->addAttribute('x', 0);
        $background->addAttribute('y', 0);
        $background->addAttribute('width', $data['outer_width']);
        $background->addAttribute('height', $data['outer_height']);
        $background->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b']));
        $background->addAttribute('fill-opacity', $this->getOpacity($qrCode->getBackgroundColor()['a']));

        foreach ($data['matrix'] as $row => $values) {
            foreach ($values as $column => $value) {
                if (1 === $value) {
                    $block = $svg->addChild('use');
                    $block->addAttribute('x', $data['margin_left'] + $data['block_size'] * $column);
                    $block->addAttribute('y', $data['margin_left'] + $data['block_size'] * $row);
                    $block->addAttribute('xlink:href', '#block', 'http://www.w3.org/1999/xlink');
                }
            }
        }

        return $svg->asXML();
    }

    private function getOpacity(int $alpha): float
    {
        $opacity = 1 - $alpha / 127;

        return $opacity;
    }

    public static function getContentType(): string
    {
        return 'image/svg+xml';
    }

    public static function getSupportedExtensions(): array
    {
        return ['svg'];
    }

    public function getName(): string
    {
        return 'svg';
    }
}
