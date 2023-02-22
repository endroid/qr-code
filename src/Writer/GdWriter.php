<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\ImageData\LabelImageData;
use Endroid\QrCode\ImageData\LogoImageData;
use Endroid\QrCode\Label\Alignment\LabelAlignmentLeft;
use Endroid\QrCode\Label\Alignment\LabelAlignmentRight;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeNone;
use Endroid\QrCode\Writer\Result\AbstractGdResult;
use Endroid\QrCode\Writer\Result\PngResult;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Zxing\QrReader;

class GdWriter implements WriterInterface, ValidatingWriterInterface
{
    public const WRITER_OPTION_RESULT_CLASS = 'result_class';
    private string $resultClass = PngResult::class;

    public function write(QrCodeInterface $qrCode, LogoInterface|null $logo = null, LabelInterface|null $label = null, array $options = []): ResultInterface
    {
        if (isset($options[self::WRITER_OPTION_RESULT_CLASS])) {
            $this->resultClass = $options[self::WRITER_OPTION_RESULT_CLASS];

            if (!is_subclass_of($this->resultClass, AbstractGdResult::class)) {
                throw new \Exception($this->resultClass . ' must extend ' . AbstractGdResult::class);
            }
        }

        if (!extension_loaded('gd')) {
            throw new \Exception('Unable to generate image: please check if the GD extension is enabled and configured correctly');
        }

        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $baseBlockSize = $qrCode->getRoundBlockSizeMode() instanceof RoundBlockSizeModeNone ? 10 : intval($matrix->getBlockSize());
        $baseImage = imagecreatetruecolor($matrix->getBlockCount() * $baseBlockSize, $matrix->getBlockCount() * $baseBlockSize);

        if (!$baseImage) {
            throw new \Exception('Unable to generate image: please check if the GD extension is enabled and configured correctly');
        }

        /** @var int $foregroundColor */
        $foregroundColor = imagecolorallocatealpha(
            $baseImage,
            $qrCode->getForegroundColor()->getRed(),
            $qrCode->getForegroundColor()->getGreen(),
            $qrCode->getForegroundColor()->getBlue(),
            $qrCode->getForegroundColor()->getAlpha()
        );

        /** @var int $transparentColor */
        $transparentColor = imagecolorallocatealpha($baseImage, 255, 255, 255, 127);

        imagefill($baseImage, 0, 0, $transparentColor);

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    imagefilledrectangle(
                        $baseImage,
                        $columnIndex * $baseBlockSize,
                        $rowIndex * $baseBlockSize,
                        ($columnIndex + 1) * $baseBlockSize - 1,
                        ($rowIndex + 1) * $baseBlockSize - 1,
                        $foregroundColor
                    );
                }
            }
        }

        $targetWidth = $matrix->getOuterSize();
        $targetHeight = $matrix->getOuterSize();

        if ($label instanceof LabelInterface) {
            $labelImageData = LabelImageData::createForLabel($label);
            $targetHeight += $labelImageData->getHeight() + $label->getMargin()->getTop() + $label->getMargin()->getBottom();
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if (!$targetImage) {
            throw new \Exception('Unable to generate image: please check if the GD extension is enabled and configured correctly');
        }

        /** @var int $backgroundColor */
        $backgroundColor = imagecolorallocatealpha(
            $targetImage,
            $qrCode->getBackgroundColor()->getRed(),
            $qrCode->getBackgroundColor()->getGreen(),
            $qrCode->getBackgroundColor()->getBlue(),
            $qrCode->getBackgroundColor()->getAlpha()
        );

        imagefill($targetImage, 0, 0, $backgroundColor);

        imagecopyresampled(
            $targetImage,
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

        if ($qrCode->getBackgroundColor()->getAlpha() > 0) {
            imagesavealpha($targetImage, true);
        }

        $result = new $this->resultClass($matrix, $targetImage, $options);

        if ($logo instanceof LogoInterface) {
            $result = $this->addLogo($logo, $result);
        }

        if ($label instanceof LabelInterface) {
            $result = $this->addLabel($label, $result);
        }

        return $result;
    }

    private function addLogo(LogoInterface $logo, AbstractGdResult $result): AbstractGdResult
    {
        $logoImageData = LogoImageData::createForLogo($logo);

        if ('image/svg+xml' === $logoImageData->getMimeType()) {
            throw new \Exception('PNG Writer does not support SVG logo');
        }

        $targetImage = $result->getImage();
        $matrix = $result->getMatrix();

        if ($logoImageData->getPunchoutBackground()) {
            /** @var int $transparent */
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagealphablending($targetImage, false);
            $xOffsetStart = intval($matrix->getOuterSize() / 2 - $logoImageData->getWidth() / 2);
            $yOffsetStart = intval($matrix->getOuterSize() / 2 - $logoImageData->getHeight() / 2);
            for ($xOffset = $xOffsetStart; $xOffset < $xOffsetStart + $logoImageData->getWidth(); ++$xOffset) {
                for ($yOffset = $yOffsetStart; $yOffset < $yOffsetStart + $logoImageData->getHeight(); ++$yOffset) {
                    imagesetpixel($targetImage, $xOffset, $yOffset, $transparent);
                }
            }
        }

        imagecopyresampled(
            $targetImage,
            $logoImageData->getImage(),
            intval($matrix->getOuterSize() / 2 - $logoImageData->getWidth() / 2),
            intval($matrix->getOuterSize() / 2 - $logoImageData->getHeight() / 2),
            0,
            0,
            $logoImageData->getWidth(),
            $logoImageData->getHeight(),
            imagesx($logoImageData->getImage()),
            imagesy($logoImageData->getImage())
        );

        return new $this->resultClass($matrix, $targetImage);
    }

    private function addLabel(LabelInterface $label, AbstractGdResult $result): AbstractGdResult
    {
        $targetImage = $result->getImage();

        $labelImageData = LabelImageData::createForLabel($label);

        /** @var int $textColor */
        $textColor = imagecolorallocatealpha(
            $targetImage,
            $label->getTextColor()->getRed(),
            $label->getTextColor()->getGreen(),
            $label->getTextColor()->getBlue(),
            $label->getTextColor()->getAlpha()
        );

        $x = intval(imagesx($targetImage) / 2 - $labelImageData->getWidth() / 2);
        $y = imagesy($targetImage) - $label->getMargin()->getBottom();

        if ($label->getAlignment() instanceof LabelAlignmentLeft) {
            $x = $label->getMargin()->getLeft();
        } elseif ($label->getAlignment() instanceof LabelAlignmentRight) {
            $x = imagesx($targetImage) - $labelImageData->getWidth() - $label->getMargin()->getRight();
        }

        imagettftext($targetImage, $label->getFont()->getSize(), 0, $x, $y, $textColor, $label->getFont()->getPath(), $label->getText());

        return new $this->resultClass($result->getMatrix(), $targetImage);
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
