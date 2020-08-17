<?php

declare(strict_types=1);

namespace Endroid\QrCode\Builder;

interface BuilderInterface
{
    public function withQrCode(): QrCodeBuilderInterface;
    public function withLogo(): LogoBuilderInterface;
    public function withLabel(): LabelBuilderInterface;
}
