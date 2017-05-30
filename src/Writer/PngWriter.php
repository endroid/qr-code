<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use Endroid\QrCode\ColorableQrCodeInterface;
use Endroid\QrCode\Exception\MissingFunctionException;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\ValidateableQrCodeInterface;
use QrReader;

class PngWriter extends AbstractBaconWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString(QrCodeInterface $qrCode)
    {
        $renderer = new Png();
        $renderer->setWidth($qrCode->getSize());
        $renderer->setHeight($qrCode->getSize());
        $renderer->setMargin(0);
        $renderer->setForegroundColor($this->convertColor($qrCode->getForegroundColor()));
        $renderer->setBackgroundColor($this->convertColor($qrCode->getBackgroundColor()));

        $writer = new Writer($renderer);
        $string = $writer->writeString($qrCode->getText(), $qrCode->getEncoding(), $this->convertErrorCorrectionLevel($qrCode->getErrorCorrectionLevel()));

        $image = imagecreatefromstring($string);
        $image = $this->addMargin($image, $qrCode->getMargin(), $qrCode->getSize(), $qrCode->getForegroundColor(), $qrCode->getBackgroundColor());

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
                     Adjust your parameters to increase readability or disable built-in validation.');
            }
        }

        return $string;
    }

    /**
     * @param resource $sourceImage
     * @param int $margin
     * @param int $size
     * @param int[] $foregroundColor
     * @param int[] $backgroundColor
     * @return resource
     */
    protected function addMargin($sourceImage, $margin, $size, array $foregroundColor, array $backgroundColor)
    {
        $additionalWhitespace = $this->calculateAdditionalWhiteSpace($sourceImage, $foregroundColor);

        if ($additionalWhitespace == 0 && $margin == 0) {
            return $sourceImage;
        }

        $targetImage = imagecreatetruecolor($size + $margin * 2, $size + $margin * 2);
        $backgroundColor = imagecolorallocate($targetImage, $backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']);
        imagefill($targetImage, 0, 0, $backgroundColor);
        imagecopyresampled($targetImage, $sourceImage, $margin, $margin, $additionalWhitespace, $additionalWhitespace, $size, $size, $size - 2 * $additionalWhitespace, $size - 2 * $additionalWhitespace);

        return $targetImage;
    }

    /**
     * @param resource $image
     * @param int[] $foregroundColor
     * @return int
     */
    protected function calculateAdditionalWhiteSpace($image, array $foregroundColor)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $foregroundColor = imagecolorallocate($image, $foregroundColor['r'], $foregroundColor['g'], $foregroundColor['b']);

        $whitespace = $width;
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $color = imagecolorat($image, $x, $y);
                if ($color == $foregroundColor || $x == $whitespace) {
                    $whitespace = min($whitespace, $x);
                    break;
                }
            }
        }

        return $whitespace;
    }

    /**
     * @param resource $sourceImage
     * @param string $logoPath
     * @param int $logoWidth
     * @return resource
     */
    protected function addLogo($sourceImage, $logoPath, $logoWidth = null)
    {
        $logoImage = imagecreatefromstring(file_get_contents($logoPath));
        $logoSourceWidth = imagesx($logoImage);
        $logoSourceHeight = imagesy($logoImage);
        $logoTargetWidth = $logoWidth;

        if ($logoTargetWidth === null) {
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

    /**
     * @param resource $sourceImage
     * @param string $label
     * @param string $labelFontPath
     * @param int $labelFontSize
     * @param string $labelAlignment
     * @param int[] $labelMargin
     * @param int[] $foregroundColor
     * @param int[] $backgroundColor
     * @return resource
     * @throws MissingFunctionException
     */
    protected function addLabel($sourceImage, $label, $labelFontPath, $labelFontSize, $labelAlignment, $labelMargin, array $foregroundColor, array $backgroundColor)
    {
        if (!function_exists('imagettfbbox')) {
            throw new MissingFunctionException('Missing function "imagettfbbox". Did you install the FreeType library?');
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

    /**
     * @param resource $image
     * @return string
     */
    protected function imageToString($image)
    {
        ob_start();
        imagepng($image);
        $string = ob_get_clean();

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    public static function getContentType()
    {
        return 'image/png';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSupportedExtensions()
    {
        return ['png'];
    }
}
