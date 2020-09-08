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
    private EncodingInterface $encoding;
    private ErrorCorrectionLevelInterface $errorCorrectionLevel;

    public function __construct(string $data, EncodingInterface $encoding = null, ErrorCorrectionLevelInterface $errorCorrectionLevel = null)
    {
        $this->data = $data;
        $this->encoding = is_null($encoding) ? new Encoding('UTF-8') : $encoding;
        $this->errorCorrectionLevel = is_null($errorCorrectionLevel) ? new ErrorCorrectionLevelLow() : $errorCorrectionLevel;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getEncoding(): EncodingInterface
    {
        return $this->encoding;
    }

    public function getErrorCorrectionLevel(): ErrorCorrectionLevelInterface
    {
        return $this->errorCorrectionLevel;
    }
}
