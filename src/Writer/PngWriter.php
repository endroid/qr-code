<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Exception\MissingFunctionException;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCodeInterface;
use Zxing\QrReader;

class PngWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        $data = $this->getData($qrCode);

        $image = $this->createImage($data, $qrCode);

        if ($qrCode->getLogoPath()) {
            $image = $this->addLogo($image, $qrCode->getLogoPath(), $qrCode->getLogoWidth());
        }

        if ($qrCode->getLabel()) {
            $image = $this->addLabel($image, $qrCode->getLabel(), $qrCode->getLabelFontPath(), $qrCode->getLabelFontSize(), $qrCode->getLabelAlignment(), $qrCode->getLabelMargin(), $qrCode->getForegroundColor(), $qrCode->getBackgroundColor());
        }

        $string = $this->imageToString($image);

        if ($qrCode->getValidateResult()) {
            $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
            if ($reader->text() !== $qrCode->getText()) {
                throw new ValidationException(
                    'Built-in validation reader read "'.$reader->text().'" instead of "'.$qrCode->getText().'".
                     Adjust your parameters to increase readability or disable built-in validation.'
                );
            }
        }

        return $string;
    }

    private function createImage(array $data, QrCodeInterface $qrCode)
    {
        $baseSize = $qrCode->getRoundBlockSize() ? $data['block_size'] : 25;

        $baseImage = $this->createBaseImage($baseSize, $data, $qrCode);
        $interpolatedImage = $this->createInterpolatedImage($baseImage, $data, $qrCode);

        return $interpolatedImage;
    }

    private function createBaseImage(int $baseSize, array $data, QrCodeInterface $qrCode)
    {
        $image = imagecreatetruecolor($data['block_count'] * $baseSize, $data['block_count'] * $baseSize);
        $foregroundColor = imagecolorallocatealpha($image, $qrCode->getForegroundColor()['r'], $qrCode->getForegroundColor()['g'], $qrCode->getForegroundColor()['b'], $qrCode->getForegroundColor()['a']);
        $backgroundColor = imagecolorallocatealpha($image, $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b'], $qrCode->getBackgroundColor()['a']);
        imagefill($image, 0, 0, $backgroundColor);

        foreach ($data['matrix'] as $row => $values) {
            foreach ($values as $column => $value) {
                if (1 === $value) {
                    imagefilledrectangle($image, $column * $baseSize, $row * $baseSize, ($column + 1) * $baseSize, ($row + 1) * $baseSize, $foregroundColor);
                }
            }
        }

        return $image;
    }

    private function createInterpolatedImage($baseImage, array $data, QrCodeInterface $qrCode)
    {
        $image = imagecreatetruecolor($data['outer_width'], $data['outer_height']);
        $backgroundColor = imagecolorallocatealpha($image, $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b'], $qrCode->getBackgroundColor()['a']);
        imagefill($image, 0, 0, $backgroundColor);
        imagecopyresampled($image, $baseImage, $data['margin_left'], $data['margin_left'], 0, 0, $data['inner_width'], $data['inner_height'], imagesx($baseImage), imagesy($baseImage));

        return $image;
    }

    private function addLogo($sourceImage, string $logoPath, int $logoWidth = null)
    {
        $logoImage = imagecreatefromstring(file_get_contents($logoPath));
        $logoSourceWidth = imagesx($logoImage);
        $logoSourceHeight = imagesy($logoImage);
        $logoTargetWidth = $logoWidth;

        if (null === $logoTargetWidth) {
            $logoTargetWidth = $logoSourceWidth;
            $logoTargetHeight = $logoSourceHeight;
        } else {
            $scale = $logoTargetWidth / $logoSourceWidth;
            $logoTargetHeight = intval($scale * imagesy($logoImage));
        }

        $logoX = imagesx($sourceImage) / 2 - $logoTargetWidth / 2;
        $logoY = imagesy($sourceImage) / 2 - $logoTargetHeight / 2;
        imagecopyresampled($sourceImage, $logoImage, $logoX, $logoY, 0, 0, $logoTargetWidth, $logoTargetHeight, $logoSourceWidth, $logoSourceHeight);

        return $sourceImage;
    }

    private function addLabel($sourceImage, string $label, string $labelFontPath, int $labelFontSize, string $labelAlignment, array $labelMargin, array $foregroundColor, array $backgroundColor)
    {
        if (!function_exists('imagettfbbox')) {
            throw new MissingFunctionException('Missing function "imagettfbbox", please make sure you installed the FreeType library');
        }

        $labelBox = imagettfbbox($labelFontSize, 0, $labelFontPath, $label);
        $labelBoxWidth = intval($labelBox[2] - $labelBox[0]);
        $labelBoxHeight = intval($labelBox[0] - $labelBox[7]);

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight + $labelBoxHeight + $labelMargin['t'] + $labelMargin['b'];

        // Create empty target image
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        $foregroundColor = imagecolorallocate($targetImage, $foregroundColor['r'], $foregroundColor['g'], $foregroundColor['b']);
        $backgroundColor = imagecolorallocate($targetImage, $backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']);
        imagefill($targetImage, 0, 0, $backgroundColor);

        // Copy source image to target image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);

        switch ($labelAlignment) {
            case LabelAlignment::LEFT:
                $labelX = $labelMargin['l'];
                break;
            case LabelAlignment::RIGHT:
                $labelX = $targetWidth - $labelBoxWidth - $labelMargin['r'];
                break;
            default:
                $labelX = intval($targetWidth / 2 - $labelBoxWidth / 2);
                break;
        }

        $labelY = $targetHeight - $labelMargin['b'];
        imagettftext($targetImage, $labelFontSize, 0, $labelX, $labelY, $foregroundColor, $labelFontPath, $label);

        return $targetImage;
    }

    private function imageToString($image): string
    {
        ob_start();
        imagepng($image);
        $string = ob_get_clean();

        return $string;
    }

    public static function getContentType(): string
    {
        return 'image/png';
    }

    public static function getSupportedExtensions(): array
    {
        return ['png'];
    }

    public function getName(): string
    {
        return 'png';
    }
}
