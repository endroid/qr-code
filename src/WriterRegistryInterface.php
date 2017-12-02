<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use Endroid\QrCode\Writer\WriterInterface;

interface WriterRegistryInterface
{
    function addWriter(WriterInterface $writer): void;
    function getWriter(string $name): WriterInterface;
    function getWriters(): array;
}
