<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

use Endroid\QrCode\Encoding;
use Endroid\QrCode\ErrorCorrectionLevelInterface;
use Endroid\QrCode\FontInterface;
use Endroid\QrCode\LabelAlignmentInterface;
use Endroid\QrCode\Writer\ResultInterface;
use Endroid\QrCode\Writer\WriterInterface;

interface BuilderInterface
{
    public function writer(WriterInterface $writer): self;
    public function data(string $data): self;
    public function encoding(Encoding $encoding): self;
    public function errorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): self;
    public function logoPath(string $logoPath): self;
    public function labelText(string $labelText): self;
    public function labelFont(FontInterface $labelFont): self;
    public function labelAlignment(LabelAlignmentInterface $labelAlignment): self;
    public function getResult(): ResultInterface;
}
