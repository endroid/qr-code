<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

class PngDataUriWriter extends AbstractDataUriWriter
{
    /**
     * {@inheritdoc}
     */
    public function getInternalWriterClass()
    {
        return PngWriter::class;
    }
}
