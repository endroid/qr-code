<?php

declare(strict_types=1);

namespace Endroid\QrCode\QrCode\Encoding;

use Endroid\QrCode\Exception\QrCodeException;

final class Encoding implements EncodingInterface
{
    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, mb_list_encodings())) {
            throw new QrCodeException(sprintf('Invalid encoding "%s"', $value));
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
