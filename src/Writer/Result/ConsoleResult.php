<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Matrix\MatrixInterface;

/**
 * Implementation of ResultInterface for printing a QR-Code on command line interface.
 */
class ConsoleResult extends AbstractResult
{
    protected MatrixInterface $matrix;
    protected string $colorEscapeCode;

    public const twoblocks = [
        0 => ' ',
        1 => "\xe2\x96\x80",
        2 => "\xe2\x96\x84",
        3 => "\xe2\x96\x88",
    ];

    /**
     * Ctor.
     */
    public function __construct(MatrixInterface $matrix, ColorInterface $foreground, ColorInterface $background)
    {
        $this->matrix = $matrix;
        $this->colorEscapeCode = sprintf(
            "\e[38;2;%d;%d;%dm\e[48;2;%d;%d;%dm",
            $foreground->getRed(),
            $foreground->getGreen(),
            $foreground->getBlue(),
            $background->getRed(),
            $background->getGreen(),
            $background->getBlue()
        );
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }

    public function getString(): string
    {
        $side = $this->matrix->getBlockCount();
        $marginLeft = $this->colorEscapeCode.self::twoblocks[0].self::twoblocks[0];
        $marginRight = self::twoblocks[0].self::twoblocks[0]."\e[0m".PHP_EOL;
        $marginVertical = $marginLeft.str_repeat(self::twoblocks[0], $side).$marginRight;

        ob_start();
        echo $marginVertical; // margin-top

        for ($rowIndex = 0; $rowIndex < $side; $rowIndex += 2) {
            echo $marginLeft;  // margin-left
            for ($columnIndex = 0; $columnIndex < $side; ++$columnIndex) {
                $combined = $this->matrix->getBlockValue($rowIndex, $columnIndex);
                if (($rowIndex + 1) < $side) {
                    $combined |= $this->matrix->getBlockValue($rowIndex + 1, $columnIndex) << 1;
                }
                echo self::twoblocks[$combined];
            }
            echo $marginRight; // margin-right
        }

        echo $marginVertical; // margin-bottom

        return (string) ob_get_clean();
    }
}
