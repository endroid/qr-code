<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use Endroid\QrCode\Enum\ErrorCorrectionLevel;

class QrCode implements QrCodeInterface
{
    private $data;
    private $errorCorrectionLevel;

    public function __construct(string $data)
    {
        $this->data = $data;

        $this->errorCorrectionLevel = ErrorCorrectionLevel::create(ErrorCorrectionLevel::HIGH);
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setErrorCorrectionLevel(string $errorCorrectionLevel): void
    {
        $this->errorCorrectionLevel = ErrorCorrectionLevel::create($errorCorrectionLevel);
    }

    public function getErrorCorrectionLevel(): string
    {
        return (string) $this->errorCorrectionLevel;
    }
}
