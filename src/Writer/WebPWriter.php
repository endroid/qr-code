<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\Result\WebPResult;

final readonly class WebPWriter implements WriterInterface, ValidatingWriterInterface
{
    use GdTrait;

    public const string WRITER_OPTION_QUALITY = 'quality';

    #[\Override]
    public function write(
        QrCodeInterface $qrCode,
        ?LogoInterface $logo = null,
        ?LabelInterface $label = null,
        array $options = [],
    ): ResultInterface {
        /** @var array{quality?: int|null} $options */
        $options[self::WRITER_OPTION_QUALITY] ??= -1;

        $gdResult = $this->writeGd($qrCode, $logo, $label, $options);

        return new WebPResult($gdResult->getMatrix(), $gdResult->getImage(), $options[self::WRITER_OPTION_QUALITY]);
    }
}
