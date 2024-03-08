<?php

declare(strict_types=1);

namespace Endroid\QrCode\Encoding;

final class Encoding implements EncodingInterface
{
    public function __construct(
        private readonly string $value
    ) {
        if ('UTF-8' !== $value) {
            if (!function_exists('mb_list_encodings')) {
                throw new \Exception('Unable to validate encoding: make sure the mbstring extension is installed and enabled');
            }

            if (!in_array($value, mb_list_encodings())) {
                throw new \Exception(sprintf('Invalid encoding "%s": choose one of '.implode(', ', mb_list_encodings()), $value));
            }
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
