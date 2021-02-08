<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\Result\SvgResult;

final class SvgWriter implements WriterInterface, LogoWriterInterface
{
    public const WRITER_OPTION_BLOCK_ID = 'block_id';
    public const WRITER_OPTION_EXCLUDE_XML_DECLARATION = 'exclude_xml_declaration';
    public const WRITER_OPTION_FORCE_XLINK_HREF = 'force_xlink_href';

    public function writeQrCode(QrCodeInterface $qrCode, array $options = []): ResultInterface
    {
        if (!isset($options[self::WRITER_OPTION_BLOCK_ID])) {
            $options[self::WRITER_OPTION_BLOCK_ID] = 'block';
        }

        if (!isset($options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION])) {
            $options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION] = false;
        }

        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $xml = new \SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
        $xml->addAttribute('version', '1.1');
        $xml->addAttribute('width', $matrix->getOuterSize().'px');
        $xml->addAttribute('height', $matrix->getOuterSize().'px');
        $xml->addAttribute('viewBox', '0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize());
        $xml->addChild('defs');

        $blockDefinition = $xml->defs->addChild('rect');
        $blockDefinition->addAttribute('id', $options[self::WRITER_OPTION_BLOCK_ID]);
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
                    $block->addAttribute('xlink:href', '#'.$options[self::WRITER_OPTION_BLOCK_ID], 'http://www.w3.org/1999/xlink');
                }
            }
        }

        return new SvgResult($xml, $options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION]);
    }

    public function writeLogo(LogoInterface $logo, ResultInterface $result, array $options = []): ResultInterface
    {
        if (!$result instanceof SvgResult) {
            throw new \Exception('Unable to write logo: instance of SvgResult expected');
        }

        if (!isset($options[self::WRITER_OPTION_FORCE_XLINK_HREF])) {
            $options[self::WRITER_OPTION_FORCE_XLINK_HREF] = false;
        }

        $xml = $result->getXml();

        $mimeType = $this->getMimeType($logoPath);
        $imageData = file_get_contents($logoPath);

        if (!is_string($imageData)) {
            throw new GenerateImageException('Unable to read image data: check your logo path');
        }

        if ('image/svg+xml' === $mimeType && (null === $logoHeight || null === $logoWidth)) {
            throw new MissingLogoHeightException('SVG Logos require an explicit height set via setLogoSize($width, $height)');
        }

        if (null === $logoHeight || null === $logoWidth) {
            $logoImage = imagecreatefromstring(strval($imageData));

            if (!$logoImage) {
                throw new GenerateImageException('Unable to generate image: check your GD installation or logo path');
            }

            /** @var mixed $logoImage */
            $logoSourceWidth = imagesx($logoImage);
            $logoSourceHeight = imagesy($logoImage);

            if (PHP_VERSION_ID < 80000) {
                imagedestroy($logoImage);
            }

            if (null === $logoWidth) {
                $logoWidth = $logoSourceWidth;
            }

            if (null === $logoHeight) {
                $aspectRatio = $logoWidth / $logoSourceWidth;
                $logoHeight = intval($logoSourceHeight * $aspectRatio);
            }
        }

        $logoX = $imageWidth / 2 - $logoWidth / 2;
        $logoY = $imageHeight / 2 - $logoHeight / 2;

        $imageDefinition = $svg->addChild('image');
        $imageDefinition->addAttribute('x', strval($logoX));
        $imageDefinition->addAttribute('y', strval($logoY));
        $imageDefinition->addAttribute('width', strval($logoWidth));
        $imageDefinition->addAttribute('height', strval($logoHeight));
        $imageDefinition->addAttribute('preserveAspectRatio', 'none');

        // xlink:href is actually deprecated, but still required when placing the qr code in a pdf.
        // SimpleXML strips out the xlink part by using addAttribute(), so it must be set directly.
        if ($forceXlinkHref) {
            $imageDefinition['xlink:href'] = 'data:'.$mimeType.';base64,'.base64_encode($imageData);
        } else {
            $imageDefinition->addAttribute('href', 'data:'.$mimeType.';base64,'.base64_encode($imageData));
        }

        // @todo Implement write SVG logo

        return $result;
    }
}
