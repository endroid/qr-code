<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label;
use Endroid\QrCode\Logo;
use Zxing\QrReader;

final class PngWriter implements LogoWriterInterface, LabelWriterInterface, ValidatingWriterInterface
{
    public function writeQrCode(): string
    {
        $image = $this->createImage($qrCode->getData(), $qrCode);





        $string = $this->imageToString($image);

        imagedestroy($image);



        return $string;
    }

    public function writeLogo(Logo $logo, ResultInterface $result)
    {
        $image = $this->addLogo($image, $logoPath, $qrCode->getLogoWidth(), $qrCode->getLogoHeight());
    }

    public function writeLabel(Label $label, ResultInterface $result)
    {
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
