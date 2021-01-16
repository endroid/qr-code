<?php

declare(strict_types=1);

namespace Endroid\QrCode\Logo;

interface LogoInterface
{
    public function getPath(): string;

    /** @return mixed */
    public function readImage();

    public function getTargetWidth(): int;

    public function getTargetHeight(): int;
}