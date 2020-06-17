<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

interface QrCodeInterface
{
    public function getData(): string;

    public function setErrorCorrectionLevel(string $errorCorrectionLevel): void;

    public function getErrorCorrectionLevel(): string;
}
