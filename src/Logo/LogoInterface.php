<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

interface LogoInterface
{
    public function getPath(): string;

    public function getResizeToWidth(): ?int;

    public function getResizeToHeight(): ?int;

    public function getImageData(): string;

    public function getImageDataUri(): string;

    /** @return mixed */
    public function getImage();

    public function getMimeType(): string;

    public function getTargetWidth(): int;

    public function getTargetHeight(): int;
}
