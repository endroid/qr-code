<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\LabelInterface;
use Endroid\QrCode\LogoInterface;
use Endroid\QrCode\Matrix;
use Endroid\QrCode\QrCodeInterface;
use Zxing\QrReader;

final class PngWriter implements LogoWriterInterface, LabelWriterInterface, ValidatingWriterInterface
{
    public function writeQrCode(QrCodeInterface $qrCode): PngResult
    {
        $matrix = Matrix::fromQrCode($qrCode);

        dump($matrix);
        die;

        $image = $this->createImage($qrCode->getData(), $qrCode);

        $logoPath = $qrCode->getLogoPath();
        if (null !== $logoPath) {
            $image = $this->addLogo($image, $logoPath, $qrCode->getLogoWidth(), $qrCode->getLogoHeight());
        }

        $label = $qrCode->getLabel();
        if (null !== $label) {
            $image = $this->addLabel($image, $label, $qrCode->getLabelFontPath(), $qrCode->getLabelFontSize(), $qrCode->getLabelAlignment(), $qrCode->getLabelMargin(), $qrCode->getForegroundColor(), $qrCode->getBackgroundColor());
        }

        $string = $this->imageToString($image);

        imagedestroy($image);

        if ($qrCode->getValidateResult()) {
            $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
            if ($reader->text() !== $qrCode->getText()) {
                throw new ValidationException('Built-in validation reader read "'.$reader->text().'" instead of "'.$qrCode->getText().'".
                     Adjust your parameters to increase readability or disable built-in validation.');
            }
        }

        return $string;

        $image = $this->createImage($qrCode->getData(), $qrCode);

        $string = $this->imageToString($image);

        imagedestroy($image);

        return $string;
    }

    public function writeLogo(LogoInterface $logo, ResultInterface $result): PngResult
    {
        if (!$result instanceof PngResult) {
            throw new \Exception('PngWriter only supports PngResult instances');
        }

        $image = $this->addLogo($image, $logoPath, $qrCode->getLogoWidth(), $qrCode->getLogoHeight());
    }

    public function writeLabel(LabelInterface $label, ResultInterface $result): PngResult
    {
        if (!$result instanceof PngResult) {
            throw new \Exception('PngWriter only supports PngResult instances');
        }

        $image = $this->addLabel($image, $label, $qrCode->getLabelFontPath(), $qrCode->getLabelFontSize(), $qrCode->getLabelAlignment(), $qrCode->getLabelMargin(), $qrCode->getForegroundColor(), $qrCode->getBackgroundColor());
    }

    public function setValidateResult(bool $validateResult): void
    {
        if ($this->validateResult) {
            $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
            if ($reader->text() !== $qrCode->getText()) {
                throw new ValidationException('Built-in validation reader read "'.$reader->text().'" instead of "'.$qrCode->getText().'".
                     Adjust your parameters to increase readability or disable built-in validation.');
            }
        }
    }
}
