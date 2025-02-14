<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\GdResult;
use Endroid\QrCode\Writer\Result\Png1bppResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final readonly class Png1bppWriter extends AbstractGdWriter
{
    public const WRITER_OPTION_COMPRESSION_LEVEL = 'compression_level';

    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []): ResultInterface
    {
        if (!isset($options[self::WRITER_OPTION_COMPRESSION_LEVEL])) {
            $options[self::WRITER_OPTION_COMPRESSION_LEVEL] = -1;
        }

        /** @var GdResult $gdResult */
        $gdResult = parent::write($qrCode, $logo, $label, $options);

        return new Png1bppResult($gdResult->getMatrix(), $gdResult->getImage(), $options[self::WRITER_OPTION_COMPRESSION_LEVEL]);
    }
}
