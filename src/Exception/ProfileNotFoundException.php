<?php

declare(strict_types=1);

namespace Endroid\QrCode\Exception;

class ProfileNotFoundException extends QrCodeException
{
    public static function create(string $name): self
    {
        return new self(sprintf('Profile with name "%s" not found', $name));
    }
}
