<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

interface ImageWriterInterface extends WriterInterface
{
    public function setSize(int $size): void;

    public function setMargin(int $margin): void;

    public function setRoundBlockSize(bool $roundBlockSize): void;
}
