<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Response;

use Endroid\QrCode\QrCodeInterface;
use Symfony\Component\HttpFoundation\Response;

if (!class_exists(Response::class)) {
    throw new \Exception('QrCodeResponse requires symfony/http-foundation');
}

class QrCodeResponse extends Response
{
    public function __construct(QrCodeInterface $qrCode)
    {
        parent::__construct($qrCode->writeString(), Response::HTTP_OK, ['Content-Type' => $qrCode->getContentType()]);
    }
}
