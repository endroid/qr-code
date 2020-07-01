<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Zxing\QrReader;

final class PngWriter extends AbstractWriter implements LabelWriterInterface, LogoWriterInterface, ValidatingWriterInterface
{
    use LabelWriterTrait;
    use LogoWriterTrait;
    use ValidatingWriterTrait;

    public function writeString(): string
    {
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

        if ($this->validateResult) {
            $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
            if ($reader->text() !== $qrCode->getText()) {
                throw new ValidationException('Built-in validation reader read "'.$reader->text().'" instead of "'.$qrCode->getText().'".
                     Adjust your parameters to increase readability or disable built-in validation.');
            }
        }

        return $string;
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }
}
