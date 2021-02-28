<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\Alignment\LabelAlignmentLeft;
use Endroid\QrCode\Label\Alignment\LabelAlignmentRight;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\PngResult;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Zxing\QrReader;

final class PngWriter implements WriterInterface, ValidatingWriterInterface
{
    public function write(QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, array $options = []): ResultInterface
    {
        if (!extension_loaded('gd')) {
            throw new \Exception('Unable to generate image: check your GD installation');
        }

        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $baseBlockSize = 50;
        $baseImage = imagecreatetruecolor($matrix->getBlockCount() * $baseBlockSize, $matrix->getBlockCount() * $baseBlockSize);

        if (!$baseImage) {
            throw new \Exception('Unable to generate image: check your GD installation');
        }

        /** @var int $foregroundColor */
        $foregroundColor = imagecolorallocatealpha(
            $baseImage,
            $qrCode->getForegroundColor()->getRed(),
            $qrCode->getForegroundColor()->getGreen(),
            $qrCode->getForegroundColor()->getBlue(),
            $qrCode->getForegroundColor()->getAlpha()
        );

        /** @var int $backgroundColor */
        $backgroundColor = imagecolorallocatealpha(
            $baseImage,
            $qrCode->getBackgroundColor()->getRed(),
            $qrCode->getBackgroundColor()->getGreen(),
            $qrCode->getBackgroundColor()->getBlue(),
            $qrCode->getBackgroundColor()->getAlpha()
        );

        imagefill($baseImage, 0, 0, $backgroundColor);

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    imagefilledrectangle(
                        $baseImage,
                        $columnIndex * $baseBlockSize,
                        $rowIndex * $baseBlockSize,
                        ($columnIndex + 1) * $baseBlockSize,
                        ($rowIndex + 1) * $baseBlockSize,
                        $foregroundColor
                    );
                }
            }
        }

        $interpolatedImage = imagecreatetruecolor($matrix->getOuterSize(), $matrix->getOuterSize());

        if (!$interpolatedImage) {
            throw new \Exception('Unable to generate image: check your GD installation');
        }

        /** @var int $backgroundColor */
        $backgroundColor = imagecolorallocatealpha(
            $interpolatedImage,
            $qrCode->getBackgroundColor()->getRed(),
            $qrCode->getBackgroundColor()->getGreen(),
            $qrCode->getBackgroundColor()->getBlue(),
            $qrCode->getBackgroundColor()->getAlpha()
        );

        imagefill($interpolatedImage, 0, 0, $backgroundColor);

        imagecopyresampled(
            $interpolatedImage,
            $baseImage,
            $matrix->getMarginLeft(),
            $matrix->getMarginLeft(),
            0,
            0,
            $matrix->getInnerSize(),
            $matrix->getInnerSize(),
            imagesx($baseImage),
            imagesy($baseImage)
        );

        if (PHP_VERSION_ID < 80000) {
            imagedestroy($baseImage);
        }

        if ($qrCode->getBackgroundColor()->getAlpha() > 0) {
            imagesavealpha($interpolatedImage, true);
        }

        $result = new PngResult($interpolatedImage);

        if ($logo instanceof LogoInterface) {
            $result = $this->addLogo($logo, $result);
        }

        if ($label instanceof LabelInterface) {
            $result = $this->addLabel($label, $result);
        }

        return $result;
    }

    public function addLogo(LogoInterface $logo, PngResult $result): PngResult
    {
        $logoImage = $logo->getImage();
        $targetImage = $result->getImage();

        imagecopyresampled(
            $targetImage,
            $logoImage,
            intval(imagesx($targetImage) / 2 - $logo->getTargetWidth() / 2),
            intval(imagesy($targetImage) / 2 - $logo->getTargetHeight() / 2),
            0,
            0,
            $logo->getTargetWidth(),
            $logo->getTargetHeight(),
            imagesx($logoImage),
            imagesy($logoImage)
        );

        if (PHP_VERSION_ID < 80000) {
            imagedestroy($logoImage);
        }

        return new PngResult($targetImage);
    }

    private function addLabel(LabelInterface $label, PngResult $result): PngResult
    {
        $sourceImage = $result->getImage();

        if (!function_exists('imagettfbbox')) {
            throw new \Exception('Function "imagettfbbox" does not exist: check your FreeType installation');
        }

        $labelBox = imagettfbbox($label->getFont()->getSize(), 0, $label->getFont()->getPath(), $label->getText());

        if (!is_array($labelBox)) {
            throw new \Exception('Unable to generate label image box: check your FreeType installation');
        }

        $labelBoxWidth = intval($labelBox[2] - $labelBox[0]);
        $labelBoxHeight = intval($labelBox[0] - $labelBox[7]);

        $targetWidth = imagesx($sourceImage);
        $targetHeight = imagesy($sourceImage) + $labelBoxHeight + $label->getMargin()->getTop() + $label->getMargin()->getBottom();

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if (!$targetImage) {
            throw new \Exception('Unable to generate image: check your GD installation');
        }

        /** @var int $textColor */
        $textColor = imagecolorallocate(
            $targetImage,
            $label->getTextColor()->getRed(),
            $label->getTextColor()->getGreen(),
            $label->getTextColor()->getBlue()
        );

        /** @var int $backgroundColor */
        $backgroundColor = imagecolorallocate(
            $targetImage,
            $label->getBackgroundColor()->getRed(),
            $label->getBackgroundColor()->getGreen(),
            $label->getBackgroundColor()->getBlue()
        );

        imagefill($targetImage, 0, 0, $backgroundColor);

        // Copy source image to target image
        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            imagesx($sourceImage),
            imagesy($sourceImage),
            imagesx($sourceImage),
            imagesy($sourceImage)
        );

        if (PHP_VERSION_ID < 80000) {
            imagedestroy($sourceImage);
        }

        $x = intval($targetWidth / 2 - $labelBoxWidth / 2);
        $y = $targetHeight - $label->getMargin()->getBottom();

        if ($label->getAlignment() instanceof LabelAlignmentLeft) {
            $x = $label->getMargin()->getLeft();
        } elseif ($label->getAlignment() instanceof LabelAlignmentRight) {
            $x = $targetWidth - $labelBoxWidth - $label->getMargin()->getRight();
        }

        imagettftext($targetImage, $label->getFont()->getSize(), 0, $x, $y, $textColor, $label->getFont()->getPath(), $label->getText());

        return new PngResult($targetImage);
    }

    public function validateResult(ResultInterface $result, string $expectedData): void
    {
        $string = $result->getString();

        if (!class_exists(QrReader::class)) {
            throw new \Exception('Please install khanamiryan/qrcode-detector-decoder or disable image validation');
        }

        if (PHP_VERSION_ID >= 80000) {
            throw new \Exception('The validator is not compatible with PHP 8 yet, see https://github.com/khanamiryan/php-qrcode-detector-decoder/pull/103');
        }

        $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
        if ($reader->text() !== $expectedData) {
            throw new \Exception('Built-in validation reader read "'.$reader->text().'" instead of "'.$expectedData.'".
                 Adjust your parameters to increase readability or disable built-in validation.');
        }
    }
}
