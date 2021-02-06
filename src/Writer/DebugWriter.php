<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\DebugResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final class DebugWriter implements WriterInterface, LabelWriterInterface, LogoWriterInterface, ValidatingWriterInterface
{
    public function writeQrCode(QrCodeInterface $qrCode, array $options = []): ResultInterface
    {
        return new DebugResult($qrCode);
    }

    public function writeLabel(LabelInterface $label, ResultInterface $result, array $options = []): ResultInterface
    {
        if (!$result instanceof DebugResult) {
            throw new \Exception('Unable to write logo: instance of DebugResult expected');
        }

        $result->setLabel($label);

        return $result;
    }

    public function writeLogo(LogoInterface $logo, ResultInterface $result, array $options = []): ResultInterface
    {
        if (!$result instanceof DebugResult) {
            throw new \Exception('Unable to write logo: instance of DebugResult expected');
        }

        $result->setLogo($logo);

        return $result;
    }

    public function validateResult(ResultInterface $result, string $expectedData): void
    {
        if (!$result instanceof DebugResult) {
            throw new \Exception('Unable to write logo: instance of DebugResult expected');
        }

        $result->setValidateResult(true);
    }
}
