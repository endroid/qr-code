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
use Endroid\QrCode\Exception\MissingFunctionException;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\LabelAlignment;
use QrReader;

class PngWriter extends AbstractBaconWriter
{
    /**
     * {@inheritdoc}
     */
    public function writeString()
    {
        $renderer = new Png();
        $renderer->setWidth($this->qrCode->getSize());
        $renderer->setHeight($this->qrCode->getSize());
        $renderer->setMargin(0);
        $renderer->setForegroundColor($this->convertColor($this->qrCode->getForegroundColor()));
        $renderer->setBackgroundColor($this->convertColor($this->qrCode->getBackgroundColor()));

        $writer = new Writer($renderer);
        $string = $writer->writeString(
            $this->qrCode->getText(),
            $this->qrCode->getEncoding(),
            $this->convertErrorCorrectionLevel($this->qrCode->getErrorCorrectionLevel())
        );

        $image = imagecreatefromstring($string);
        $image = $this->addMargin($image);
        $image = $this->addLogo($image);
        $image = $this->addLabel($image);
        $string = $this->imageToString($image);

        if ($this->qrCode->getValidateResult()) {
            $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
            if ($reader->text() !== $this->qrCode->getText()) {
                throw new ValidationException(
                    'Built-in validation reader read "'.$reader->text().'" instead of "'.$this->qrCode->getText().'".
                     Adjust your parameters to increase readability or disable built-in validation.');
            }
        }

        return $string;
    }

    /**
     * @param resource $sourceImage
     * @return resource
     */
    protected function addMargin($sourceImage)
    {
        $additionalWhitespace = $this->calculateAdditionalWhiteSpace($sourceImage);

        if ($additionalWhitespace == 0 && $this->qrCode->getMargin() == 0) {
            return $sourceImage;
        }

        $targetImage = imagecreatetruecolor($this->qrCode->getSize() + $this->qrCode->getMargin() * 2, $this->qrCode->getSize() + $this->qrCode->getMargin() * 2);
        $backgroundColor = imagecolorallocate($targetImage, $this->qrCode->getBackgroundColor()['r'], $this->qrCode->getBackgroundColor()['g'], $this->qrCode->getBackgroundColor()['b']);
        imagefill($targetImage, 0, 0, $backgroundColor);
        imagecopyresampled($targetImage, $sourceImage, $this->qrCode->getMargin(), $this->qrCode->getMargin(), $additionalWhitespace, $additionalWhitespace, $this->qrCode->getSize(), $this->qrCode->getSize(), $this->qrCode->getSize() - 2 * $additionalWhitespace, $this->qrCode->getSize() - 2 * $additionalWhitespace);

        return $targetImage;
    }

    /**
     * @param resource $image
     * @return int
     */
    protected function calculateAdditionalWhiteSpace($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $foregroundColor = $this->qrCode->getForegroundColor();
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
     * @return resource
     */
    protected function addLogo($sourceImage)
    {
        if ($this->qrCode->getLogoPath() === null) {
            return $sourceImage;
        }

        $logoImage = imagecreatefromstring(file_get_contents($this->qrCode->getLogoPath()));
        $logoSourceWidth = imagesx($logoImage);
        $logoSourceHeight = imagesy($logoImage);
        $logoTargetWidth = $this->qrCode->getLogoSize();

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
     * @return resource
     * @throws MissingFunctionException
     */
    protected function addLabel($sourceImage)
    {
        if ($this->qrCode->getLabel() === null) {
            return $sourceImage;
        }

        if (!function_exists('imagettfbbox')) {
            throw new MissingFunctionException('Missing function "imagettfbbox". Did you install the FreeType library?');
        }

        $labelBox = imagettfbbox($this->qrCode->getLabelFontSize(), 0, $this->qrCode->getLabelFontPath(), $this->qrCode->getLabel());
        $labelBoxWidth = intval($labelBox[2] - $labelBox[0]);
        $labelBoxHeight = intval($labelBox[0] - $labelBox[7]);
        $labelMargin = $this->qrCode->getLabelMargin();

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight + $labelBoxHeight + $labelMargin['t'] + $labelMargin['b'];

        // Create empty target image
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        $foregroundColor = imagecolorallocate($targetImage, $this->qrCode->getForegroundColor()['r'], $this->qrCode->getForegroundColor()['g'], $this->qrCode->getForegroundColor()['b']);
        $backgroundColor = imagecolorallocate($targetImage, $this->qrCode->getBackgroundColor()['r'], $this->qrCode->getBackgroundColor()['g'], $this->qrCode->getBackgroundColor()['b']);
        imagefill($targetImage, 0, 0, $backgroundColor);

        // Copy source image to target image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);

        switch ($this->qrCode->getLabelAlignment()) {
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
        imagettftext($targetImage, $this->qrCode->getLabelFontSize(), 0, $labelX, $labelY, $foregroundColor, $this->qrCode->getLabelFontPath(), $this->qrCode->getLabel());

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
    public function getContentType()
    {
        return 'image/png';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedExtensions()
    {
        return ['png'];
    }
}
