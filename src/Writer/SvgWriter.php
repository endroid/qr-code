<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Exception\MissingExtensionException;
use Endroid\QrCode\Exception\MissingLogoHeightException;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\QrCodeInterface;
use SimpleXMLElement;

class SvgWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        if ($qrCode->getValidateResult()) {
            throw new ValidationException('Built-in validation reader can not check SVG images: please disable via setValidateResult(false)');
        }

        $data = $qrCode->getData();

        $svg = new SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
        $svg->addAttribute('version', '1.1');
        $svg->addAttribute('width', $data['outer_width'].'px');
        $svg->addAttribute('height', $data['outer_height'].'px');
        $svg->addAttribute('viewBox', '0 0 '.$data['outer_width'].' '.$data['outer_height']);
        $svg->addChild('defs');

        // Block definition
        $blockDefinition = $svg->defs->addChild('rect');
        $blockDefinition->addAttribute('id', 'block');
        $blockDefinition->addAttribute('width', strval($data['block_size']));
        $blockDefinition->addAttribute('height', strval($data['block_size']));
        $blockDefinition->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()['r'], $qrCode->getForegroundColor()['g'], $qrCode->getForegroundColor()['b']));
        $blockDefinition->addAttribute('fill-opacity', strval($this->getOpacity($qrCode->getForegroundColor()['a'])));

        // Background
        $background = $svg->addChild('rect');
        $background->addAttribute('x', '0');
        $background->addAttribute('y', '0');
        $background->addAttribute('width', strval($data['outer_width']));
        $background->addAttribute('height', strval($data['outer_height']));
        $background->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b']));
        $background->addAttribute('fill-opacity', strval($this->getOpacity($qrCode->getBackgroundColor()['a'])));

        foreach ($data['matrix'] as $row => $values) {
            foreach ($values as $column => $value) {
                if (1 === $value) {
                    $block = $svg->addChild('use');
                    $block->addAttribute('x', strval($data['margin_left'] + $data['block_size'] * $column));
                    $block->addAttribute('y', strval($data['margin_left'] + $data['block_size'] * $row));
                    $block->addAttribute('xlink:href', '#block', 'http://www.w3.org/1999/xlink');
                }
            }
        }

        if ($qrCode->getLogoPath()) {
            $this->addLogo($svg, $data['outer_width'], $data['outer_height'], $qrCode->getLogoPath(), $qrCode->getLogoWidth(), $qrCode->getLogoHeight());
        }

        $xml = $svg->asXML();

        $options = $qrCode->getWriterOptions();
        if (isset($options['exclude_xml_declaration']) && $options['exclude_xml_declaration']) {
            $xml = str_replace("<?xml version=\"1.0\"?>\n", '', $xml);
        }

        return $xml;
    }

    private function addLogo(SimpleXMLElement $svg, int $imageWidth, int $imageHeight, string $logoPath, int $logoWidth, int $logoHeight = null): void
    {
        $mimeType = $this->getMimeType($logoPath);
        $imageData = file_get_contents($logoPath);

        if (null === $logoHeight) {
            if ('image/svg+xml' === $mimeType) {
                throw new MissingLogoHeightException('SVG Logos require an explicit height set via setLogoSize($width, $height)');
            } else {
                $logoImage = imagecreatefromstring($imageData);
                $aspectRatio = $logoWidth / imagesx($logoImage);
                $logoHeight = intval(imagesy($logoImage) * $aspectRatio);
            }
        }

        $imageDefinition = $svg->addChild('image');
        $imageDefinition->addAttribute('x', strval($imageWidth / 2 - $logoWidth / 2));
        $imageDefinition->addAttribute('y', strval($imageHeight / 2 - $logoHeight / 2));
        $imageDefinition->addAttribute('width', strval($logoWidth));
        $imageDefinition->addAttribute('height', strval($logoHeight));
        $imageDefinition->addAttribute('preserveAspectRatio', 'none');
        $imageDefinition->addAttribute('xlink:href', 'data:'.$mimeType.';base64,'.base64_encode($imageData));
    }

    private function getMimeType(string $path): string
    {
        if (!function_exists('mime_content_type')) {
            throw new MissingExtensionException('You need the ext-fileinfo extension to determine the mime type');
        }

        $mimeType = mime_content_type($path);

        // Passing mime type image/svg results in invisible images
        if ('image/svg' === $mimeType) {
            return 'image/svg+xml';
        }

        return $mimeType;
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
