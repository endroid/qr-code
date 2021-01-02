<?php

declare(strict_types=1);

namespace Endroid\QrCode\Label\Font;

final class NotoSans implements FontInterface
{
    /** @var int */
    private $size;

    public function __construct(int $size = 16)
    {
        $this->size = $size;
    }

    public function getPath(): string
    {
        return __DIR__.'/../../../assets/noto_sans.otf';
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
