<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\DebugWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;

class StaticWriterRegistry extends WriterRegistry
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->loadWriters();
    }

    protected function loadWriters()
    {
        if (count($this->writers) > 0) {
            return;
        }

        $this->addWriter(new BinaryWriter());
        $this->addWriter(new DebugWriter());
        $this->addWriter(new EpsWriter());
        $this->addWriter(new PngWriter(), true);
        $this->addWriter(new SvgWriter());
    }
}
