<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Exception\QrCodeException;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\RoundBlockSizeMode\RoundBlockSizeModeInterface;

final class WriterBuilder implements WriterBuilderInterface
{
    private string $className;

    public static function create(): self
    {
        return new static();
    }

    public function className(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function build(): WriterInterface
    {
        return new $this->className();
    }
}
