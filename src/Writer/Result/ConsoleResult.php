<?php

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\Writer\Result\AbstractResult;

/**
 * Implementation of ResultInterface for printing a QR-Code on command line interface
 */
class ConsoleResult extends AbstractResult
{

    const TWOBLOCKS = [
        0 => "\xe2\x96\x88",
        1 => "\xe2\x96\x84",
        2 => "\xe2\x96\x80",
        3 => ' '
    ];

    protected MatrixInterface $matrix;
    protected array $twoblocks;

    /**
     * @param MatrixInterface $matrix
     * @param bool $darkmode Darkmode means white characters on a dark background
     */
    public function __construct(MatrixInterface $matrix, bool $darkmode)
    {
        $this->matrix = $matrix;
        $this->twoblocks = $darkmode ? self::TWOBLOCKS : array_reverse(self::TWOBLOCKS);
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }

    public function getString(): string
    {
        $side = $this->matrix->getBlockCount();
        $margin = $this->twoblocks[0] . $this->twoblocks[0];

        ob_start();
        echo str_repeat($this->twoblocks[0], $side + 4) . PHP_EOL; // margin-top

        for ($rowIndex = 0; $rowIndex < $side; $rowIndex += 2) {
            echo $margin;  // margin-left
            for ($columnIndex = 0; $columnIndex < $side; $columnIndex++) {
                $combined = $this->matrix->getBlockValue($rowIndex, $columnIndex);
                if (($rowIndex + 1) < $side) {
                    $combined |= $this->matrix->getBlockValue($rowIndex + 1, $columnIndex) << 1;
                }
                echo $this->twoblocks[$combined];
            }
            echo $margin . PHP_EOL; // margin-right
        }

        echo str_repeat($this->twoblocks[0], $side + 4) . PHP_EOL; // margin-bottom

        return (string) ob_get_clean();
    }

}
