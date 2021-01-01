<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;

final class PngWriter implements WriterInterface, LabelWriterInterface, LogoWriterInterface, ValidatingWriterInterface
{
    private const BASE_BLOCK_SIZE = 50;

    public function writeQrCode(QrCodeInterface $qrCode): ResultInterface
    {
        if (!extension_loaded('gd')) {
            throw new \Exception('Unable to generate image: check your GD installation');
        }

        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $baseImage = imagecreatetruecolor($matrix->getBlockCount() * self::BASE_BLOCK_SIZE, $matrix->getBlockCount() * self::BASE_BLOCK_SIZE);

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

        foreach ($matrix->getIterator() as $rowIndex => $rowIterator) {
            foreach ($rowIterator as $columnIndex => $value) {
                if (1 === $value) {
                    imagefilledrectangle(
                        $baseImage,
                        $columnIndex * self::BASE_BLOCK_SIZE,
                        $rowIndex * self::BASE_BLOCK_SIZE,
                        ($columnIndex + 1) * self::BASE_BLOCK_SIZE,
                        ($rowIndex + 1) * self::BASE_BLOCK_SIZE,
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

        return new PngResult($interpolatedImage);
    }

    public function writeLogo(LogoInterface $logo, ResultInterface $result): ResultInterface
    {
        if (!$result instanceof PngResult) {
            throw new \Exception('Unable to write logo: instance of PngResult expected');
        }

        $image = $result->getImage();
        $logoImage = $logo->readImage();

        imagecopyresampled(
            $image,
            $logoImage,
            intval(imagesx($image) / 2 - $logo->getTargetWidth() / 2),
            intval(imagesy($image) / 2 - $logo->getTargetHeight() / 2),
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

        return $result;
    }

    ///

//        imagedestroy($image);
//
//        if ($qrCode->getValidateResult()) {
//            $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
//            if ($reader->text() !== $qrCode->getText()) {
//                throw new ValidationException('Built-in validation reader read "'.$reader->text().'" instead of "'.$qrCode->getText().'".
//                     Adjust your parameters to increase readability or disable built-in validation.');
//            }
//        }
//
//        return $string;
//    }

    public function writeLabel(LabelInterface $label, ResultInterface $result): ResultInterface
    {
        if (!$result instanceof PngResult) {
            throw new QrCodeException('PngWriter only supports PngResult instances');
        }

        $image = $this->addLabel($image, $label, $qrCode->getLabelFontPath(), $qrCode->getLabelFontSize(), $qrCode->getLabelAlignment(), $qrCode->getLabelMargin(), $qrCode->getForegroundColor(), $qrCode->getBackgroundColor());
    }
}
