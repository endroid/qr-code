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
    /**
     * @param WriterInterface $writer
     *
     * @return $this
     */
    public function addWriter(WriterInterface $writer);

    /**
     * @param $name
     *
     * @return WriterInterface
     */
    public function getWriter($name);

    /**
     * @return WriterInterface[]
     */
    public function getWriters();
}
