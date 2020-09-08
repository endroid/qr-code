<?php

declare(strict_types=1);

namespace Endroid\QrCode;

final class Encoding implements EncodingInterface
{
    private string $name;

    public function __construct(string $name)
    {
        if (!in_array($name, mb_list_encodings())) {
            throw new \Exception(sprintf('Invalid encoding "%s"', $name));
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
