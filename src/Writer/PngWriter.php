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
        $renderer->setMargin($this->qrCode->getQuietZone());
        $renderer->setForegroundColor($this->convertColor($this->qrCode->getForegroundColor()));
        $renderer->setBackgroundColor($this->convertColor($this->qrCode->getBackgroundColor()));

        $writer = new Writer($renderer);
        $string = $writer->writeString(
            $this->qrCode->getText(),
            $this->qrCode->getEncoding(),
            $this->convertErrorCorrectionLevel($this->qrCode->getErrorCorrectionLevel())
        );

        if ($this->qrCode->getLabel() !== null || $this->qrCode->getLogoPath() !== null) {
            $image = imagecreatefromstring($string);
            $image = $this->addLogo($image);
            $image = $this->addLabel($image);
            $string = $this->imageToString($image);
        }

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
    private function addLogo($sourceImage)
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
    private function addLabel($sourceImage)
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

        $labelX = intval($targetWidth / 2 - $labelBoxWidth / 2) + $labelMargin['l'];
        $labelY = $targetHeight - $labelMargin['b'];
        imagettftext($targetImage, $this->qrCode->getLabelFontSize(), 0, $labelX, $labelY, $foregroundColor, $this->qrCode->getLabelFontPath(), $this->qrCode->getLabel());

        return $targetImage;
    }

    /**
     * @param resource $image
     * @return string
     */
    private function imageToString($image)
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
    public function getSupportedExtensions()
    {
        return ['png'];
    }
}
