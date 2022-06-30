<?php

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ConsoleResult;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\WriterInterface;

/**
 * Writer of QR Code for CLI
 */
class ConsoleWriter implements WriterInterface
{

    protected $darkmode;

    /**
     * Ctor
     * @param bool $darkmode Darkmode means white characters on a dark background (default: true)
     */
    public function __construct(bool $darkmode = true)
    {
        $this->darkmode = $darkmode;
    }

    /**
     * @inheritDoc
     */
    public function write(QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, $options = []): ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        return new ConsoleResult($matrix, $this->darkmode);
    }

}
