<?php

declare(strict_types=1);

namespace Endroid\QrCode\Encoding;

final class Encoding implements EncodingInterface
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        if (!in_array($value, mb_list_encodings())) {
            throw new \Exception(sprintf('Invalid encoding "%s"', $value));
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
