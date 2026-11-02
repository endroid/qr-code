<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\GifResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final readonly class GifWriter implements WriterInterface, ValidatingWriterInterface
{
    use GdTrait;

    #[\Override]
    public function write(
        QrCodeInterface $qrCode,
        ?LogoInterface $logo = null,
        ?LabelInterface $label = null,
        array $options = [],
    ): ResultInterface {
        $gdResult = $this->writeGd($qrCode, $logo, $label, $options);

        return new GifResult($gdResult->getMatrix(), $gdResult->getImage());
    }
}
