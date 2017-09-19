<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

use Endroid\QrCode\Exception\InvalidWriterException;
use Endroid\QrCode\Writer\WriterInterface;

class WriterRegistry implements WriterRegistryInterface
{
    /**
     * @var WriterInterface[]
     */
    protected $writers;

    /**
     * @var WriterInterface
     */
    protected $defaultWriter;

    public function __construct()
    {
        $this->writers = [];
    }

    /**
     * {@inheritdoc}
     */
    public function addWriter(WriterInterface $writer, $setAsDefault = false)
    {
        $this->writers[$writer->getName()] = $writer;

        if ($setAsDefault || 1 === count($this->writers)) {
            $this->defaultWriter = $writer;
        }
    }

    /**
     * @param $name
     *
     * @return WriterInterface
     */
    public function getWriter($name)
    {
        $this->assertValidWriter($name);

        return $this->writers[$name];
    }

    /**
     * @return WriterInterface
     *
     * @throws InvalidWriterException
     */
    public function getDefaultWriter()
    {
        if ($this->defaultWriter instanceof WriterInterface) {
            return $this->defaultWriter;
        }

        throw new InvalidWriterException('Please set the default writer via the second argument of addWriter');
    }

    /**
     * @return WriterInterface[]
     */
    public function getWriters()
    {
        return $this->writers;
    }

    /**
     * @param string $writer
     *
     * @throws InvalidWriterException
     */
    protected function assertValidWriter($writer)
    {
        if (!isset($this->writers[$writer])) {
            throw new InvalidWriterException('Invalid writer "'.$writer.'"');
        }
    }
}
