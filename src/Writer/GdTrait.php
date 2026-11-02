<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\ImageData\LabelImageData;
use Endroid\QrCode\ImageData\LogoImageData;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\Result\GdResult;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Zxing\QrReader;

trait GdTrait
{
    public function getMatrix(QrCodeInterface $qrCode): MatrixInterface
    {
        $matrixFactory = new MatrixFactory();

        return $matrixFactory->create($qrCode);
    }

    /** @param array<string, mixed> $options */
    public function writeGd(
        QrCodeInterface $qrCode,
        ?LogoInterface $logo = null,
        ?LabelInterface $label = null,
        array $options = [],
    ): GdResult {
        // Options are reserved for format-specific writers; GD rendering is shared.
        unset($options);

        if (!extension_loaded('gd')) {
            throw new \Exception(
                'Unable to generate image: please check if the GD extension is enabled and configured correctly',
            );
        }

        $matrix = $this->getMatrix($qrCode);
        $baseImage = $this->createBaseImage($qrCode, $matrix);

        /** @var int<1, max> $targetWidth */
        $targetWidth = $matrix->getOuterSize();
        /** @var int<1, max> $targetHeight */
        $targetHeight = $matrix->getOuterSize();

        if ($label instanceof LabelInterface) {
            $labelImageData = LabelImageData::createForLabel($label);
            $targetHeight +=
                $labelImageData->getHeight() + $label->getMargin()->getTop() + $label->getMargin()->getBottom();
        }

        /** @var int<1, max> $targetHeight */
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if (!$targetImage) {
            throw new \Exception(
                'Unable to generate image: please check if the GD extension is enabled and configured correctly',
            );
        }

        $backgroundColor = imagecolorallocatealpha(
            $targetImage,
            $qrCode->getBackgroundColor()->getRed(),
            $qrCode->getBackgroundColor()->getGreen(),
            $qrCode->getBackgroundColor()->getBlue(),
            $qrCode->getBackgroundColor()->getAlpha(),
        );

        if (false === $backgroundColor) {
            throw new \Exception(
                'Unable to generate image: please check if the GD extension is enabled and configured correctly',
            );
        }

        imagefill($targetImage, x: 0, y: 0, color: $backgroundColor);

        imagecopyresampled(
            $targetImage,
            $baseImage,
            $matrix->getMarginLeft(),
            $matrix->getMarginLeft(),
            src_x: 0,
            src_y: 0,
            dst_width: $matrix->getInnerSize(),
            dst_height: $matrix->getInnerSize(),
            src_width: imagesx($baseImage),
            src_height: imagesy($baseImage),
        );

        if ($qrCode->getBackgroundColor()->getAlpha() > 0) {
            imagesavealpha($targetImage, enable: true);
        }

        $result = new GdResult($matrix, $targetImage);

        if ($logo instanceof LogoInterface) {
            $result = $this->addLogo($logo, $result);
        }

        if ($label instanceof LabelInterface) {
            $result = $this->addLabel($label, $result);
        }

        return $result;
    }

    private function createBaseImage(QrCodeInterface $qrCode, MatrixInterface $matrix): \GdImage
    {
        $baseBlockSize = RoundBlockSizeMode::None === $qrCode->getRoundBlockSizeMode()
            ? 10
            : intval($matrix->getBlockSize());
        /** @var int<1, max> $baseImageSize */
        $baseImageSize = $matrix->getBlockCount() * $baseBlockSize;
        $baseImage = imagecreatetruecolor($baseImageSize, $baseImageSize);

        if (!$baseImage) {
            throw new \Exception(
                'Unable to generate image: please check if the GD extension is enabled and configured correctly',
            );
        }

        $foregroundColor = imagecolorallocatealpha(
            $baseImage,
            $qrCode->getForegroundColor()->getRed(),
            $qrCode->getForegroundColor()->getGreen(),
            $qrCode->getForegroundColor()->getBlue(),
            $qrCode->getForegroundColor()->getAlpha(),
        );

        if (false === $foregroundColor) {
            throw new \Exception(
                'Unable to generate image: please check if the GD extension is enabled and configured correctly',
            );
        }

        $transparentColor = imagecolorallocatealpha($baseImage, red: 255, green: 255, blue: 255, alpha: 127);

        if (false === $transparentColor) {
            throw new \Exception(
                'Unable to generate image: please check if the GD extension is enabled and configured correctly',
            );
        }

        imagefill($baseImage, x: 0, y: 0, color: $transparentColor);

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 !== $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    continue;
                }

                imagefilledrectangle(
                    $baseImage,
                    $columnIndex * $baseBlockSize,
                    $rowIndex * $baseBlockSize,
                    (($columnIndex + 1) * $baseBlockSize) - 1,
                    (($rowIndex + 1) * $baseBlockSize) - 1,
                    $foregroundColor,
                );
            }
        }

        return $baseImage;
    }

    private function addLogo(LogoInterface $logo, GdResult $result): GdResult
    {
        $logoImageData = LogoImageData::createForLogo($logo);

        if ('image/svg+xml' === $logoImageData->getMimeType()) {
            throw new \Exception('GD Writer does not support SVG logo');
        }

        $targetImage = $result->getImage();
        $matrix = $result->getMatrix();

        if ($logoImageData->getPunchoutBackground()) {
            $transparent = imagecolorallocatealpha($targetImage, red: 255, green: 255, blue: 255, alpha: 127);

            if (false === $transparent) {
                throw new \Exception(
                    'Unable to allocate color: please check if the GD extension is enabled and configured correctly',
                );
            }

            imagealphablending($targetImage, enable: false);
            $xOffsetStart = intval(($matrix->getOuterSize() / 2) - ($logoImageData->getWidth() / 2));
            $yOffsetStart = intval(($matrix->getOuterSize() / 2) - ($logoImageData->getHeight() / 2));
            for ($xOffset = $xOffsetStart; $xOffset < ($xOffsetStart + $logoImageData->getWidth()); ++$xOffset) {
                for ($yOffset = $yOffsetStart; $yOffset < ($yOffsetStart + $logoImageData->getHeight()); ++$yOffset) {
                    imagesetpixel($targetImage, $xOffset, $yOffset, $transparent);
                }
            }
        }

        imagecopyresampled(
            $targetImage,
            $logoImageData->getImage(),
            intval(($matrix->getOuterSize() / 2) - ($logoImageData->getWidth() / 2)),
            intval(($matrix->getOuterSize() / 2) - ($logoImageData->getHeight() / 2)),
            src_x: 0,
            src_y: 0,
            dst_width: $logoImageData->getWidth(),
            dst_height: $logoImageData->getHeight(),
            src_width: imagesx($logoImageData->getImage()),
            src_height: imagesy($logoImageData->getImage()),
        );

        return new GdResult($matrix, $targetImage);
    }

    private function addLabel(LabelInterface $label, GdResult $result): GdResult
    {
        $targetImage = $result->getImage();

        $labelImageData = LabelImageData::createForLabel($label);

        $textColor = imagecolorallocatealpha(
            $targetImage,
            $label->getTextColor()->getRed(),
            $label->getTextColor()->getGreen(),
            $label->getTextColor()->getBlue(),
            $label->getTextColor()->getAlpha(),
        );

        if (false === $textColor) {
            throw new \Exception(
                'Unable to allocate color: please check if the GD extension is enabled and configured correctly',
            );
        }

        $x = match ($label->getAlignment()) {
            LabelAlignment::Left => $label->getMargin()->getLeft(),
            LabelAlignment::Right => imagesx($targetImage) - $labelImageData->getWidth()
                - $label->getMargin()->getRight(),
            default => intval((imagesx($targetImage) / 2) - ($labelImageData->getWidth() / 2)),
        };
        $y = imagesy($targetImage) - $label->getMargin()->getBottom();

        imagettftext(
            $targetImage,
            $label->getFont()->getSize(),
            angle: 0,
            x: $x,
            y: $y,
            color: $textColor,
            font_filename: $label->getFont()->getPath(),
            text: $label->getText(),
        );

        return new GdResult($result->getMatrix(), $targetImage);
    }

    public function validateResult(ResultInterface $result, string $expectedData): void
    {
        $string = $result->getString();

        if (!class_exists(QrReader::class)) {
            throw ValidationException::createForMissingPackage('khanamiryan/qrcode-detector-decoder');
        }

        $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
        if ($reader->text() !== $expectedData) {
            throw ValidationException::createForInvalidData($expectedData, strval($reader->text()));
        }
    }
}
