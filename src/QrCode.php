<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

final class QrCode implements QrCodeInterface
{
    private string $data;
    private Encoding $encoding;
    private ErrorCorrectionLevel $errorCorrectionLevel;

    public function __construct(string $data, Encoding $encoding = null, ErrorCorrectionLevel $errorCorrectionLevel = null)
    {
        $this->data = $data;
        $this->encoding = is_null($encoding) ? new Encoding('UTF-8') : $encoding;
        $this->errorCorrectionLevel = is_null($errorCorrectionLevel) ? new ErrorCorrectionLevel('low') : $errorCorrectionLevel;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getEncoding(): Encoding
    {
        return $this->encoding;
    }

    public function getErrorCorrectionLevel(): ErrorCorrectionLevel
    {
        return $this->errorCorrectionLevel;
    }
}
