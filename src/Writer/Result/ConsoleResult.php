<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Matrix\MatrixInterface;

/**
 * Implementation of ResultInterface for printing a QR-Code on command line interface.
 */
class ConsoleResult extends AbstractResult
{
    protected MatrixInterface $matrix;
    protected $bgIndex;
    protected $fgIndex;

    const twoblocks = [
        0 => ' ',
        1 => "\xe2\x96\x80",
        2 => "\xe2\x96\x84",
        3 => "\xe2\x96\x88",
    ];

    // RGB colors mapping
    // https://upload.wikimedia.org/wikipedia/commons/1/15/Xterm_256color_chart.svg
    public const xtermRgbColor = [
        '000000', '800000', '008000', '808000', '000080',
        '800080', '008080', 'c0c0c0', '808080', 'ff0000', '00ff00', 'ffff00',
        '0000ff', 'ff00ff', '00ffff', 'ffffff', '000000', '00005f', '000087',
        '0000af', '0000d7', '0000ff', '005f00', '005f5f', '005f87', '005faf',
        '005fd7', '005fff', '008700', '00875f', '008787', '0087af', '0087d7',
        '0087ff', '00af00', '00af5f', '00af87', '00afaf', '00afd7', '00afff',
        '00d700', '00d75f', '00d787', '00d7af', '00d7d7', '00d7ff', '00ff00',
        '00ff5f', '00ff87', '00ffaf', '00ffd7', '00ffff', '5f0000', '5f005f',
        '5f0087', '5f00af', '5f00d7', '5f00ff', '5f5f00', '5f5f5f', '5f5f87',
        '5f5faf', '5f5fd7', '5f5fff', '5f8700', '5f875f', '5f8787', '5f87af',
        '5f87d7', '5f87ff', '5faf00', '5faf5f', '5faf87', '5fafaf', '5fafd7',
        '5fafff', '5fd700', '5fd75f', '5fd787', '5fd7af', '5fd7d7', '5fd7ff',
        '5fff00', '5fff5f', '5fff87', '5fffaf', '5fffd7', '5fffff', '870000',
        '87005f', '870087', '8700af', '8700d7', '8700ff', '875f00', '875f5f',
        '875f87', '875faf', '875fd7', '875fff', '878700', '87875f', '878787',
        '8787af', '8787d7', '8787ff', '87af00', '87af5f', '87af87', '87afaf',
        '87afd7', '87afff', '87d700', '87d75f', '87d787', '87d7af', '87d7d7',
        '87d7ff', '87ff00', '87ff5f', '87ff87', '87ffaf', '87ffd7', '87ffff',
        'af0000', 'af005f', 'af0087', 'af00af', 'af00d7', 'af00ff', 'af5f00',
        'af5f5f', 'af5f87', 'af5faf', 'af5fd7', 'af5fff', 'af8700', 'af875f',
        'af8787', 'af87af', 'af87d7', 'af87ff', 'afaf00', 'afaf5f', 'afaf87',
        'afafaf', 'afafd7', 'afafff', 'afd700', 'afd75f', 'afd787', 'afd7af',
        'afd7d7', 'afd7ff', 'afff00', 'afff5f', 'afff87', 'afffaf', 'afffd7',
        'afffff', 'd70000', 'd7005f', 'd70087', 'd700af', 'd700d7', 'd700ff',
        'd75f00', 'd75f5f', 'd75f87', 'd75faf', 'd75fd7', 'd75fff', 'd78700',
        'd7875f', 'd78787', 'd787af', 'd787d7', 'd787ff', 'dfaf00', 'dfaf5f',
        'dfaf87', 'dfafaf', 'dfafdf', 'dfafff', 'dfdf00', 'dfdf5f', 'dfdf87',
        'dfdfaf', 'dfdfdf', 'dfdfff', 'dfff00', 'dfff5f', 'dfff87', 'dfffaf',
        'dfffdf', 'dfffff', 'ff0000', 'ff005f', 'ff0087', 'ff00af', 'ff00df',
        'ff00ff', 'ff5f00', 'ff5f5f', 'ff5f87', 'ff5faf', 'ff5fdf', 'ff5fff',
        'ff8700', 'ff875f', 'ff8787', 'ff87af', 'ff87df', 'ff87ff', 'ffaf00',
        'ffaf5f', 'ffaf87', 'ffafaf', 'ffafdf', 'ffafff', 'ffdf00', 'ffdf5f',
        'ffdf87', 'ffdfaf', 'ffdfdf', 'ffdfff', 'ffff00', 'ffff5f', 'ffff87',
        'ffffaf', 'ffffdf', 'ffffff', '080808', '121212', '1c1c1c', '262626',
        '303030', '3a3a3a', '444444', '4e4e4e', '585858', '626262', '6c6c6c',
        '767676', '808080', '8a8a8a', '949494', '9e9e9e', 'a8a8a8', 'b2b2b2',
        'bcbcbc', 'c6c6c6', 'd0d0d0', 'dadada', 'e4e4e4', 'eeeeee',
    ];

    /**
     * @param bool $darkmode Darkmode means white characters on a dark background
     */
    public function __construct(MatrixInterface $matrix, ColorInterface $foreground, ColorInterface $background)
    {
        $this->matrix = $matrix;
        $this->bgIndex = $this->findClosestColor($background);
        $this->fgIndex = $this->findClosestColor($foreground);
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }

    public function getString(): string
    {
        $side = $this->matrix->getBlockCount();
        $margin = self::twoblocks[0] . self::twoblocks[0];

        ob_start();
        // forefround color
        echo "\e[38;5;{$this->fgIndex}m";
        // background color
        echo "\e[48;5;{$this->bgIndex}m";

        echo str_repeat(self::twoblocks[0], $side + 4) . PHP_EOL; // margin-top

        for ($rowIndex = 0; $rowIndex < $side; $rowIndex += 2) {
            echo $margin;  // margin-left
            for ($columnIndex = 0; $columnIndex < $side; ++$columnIndex) {
                $combined = $this->matrix->getBlockValue($rowIndex, $columnIndex);
                if (($rowIndex + 1) < $side) {
                    $combined |= $this->matrix->getBlockValue($rowIndex + 1, $columnIndex) << 1;
                }
                echo self::twoblocks[$combined];
            }
            echo $margin . PHP_EOL; // margin-right
        }

        echo str_repeat(self::twoblocks[0], $side + 4) . PHP_EOL; // margin-bottom
        echo "\e[0m";

        return (string) ob_get_clean();
    }

    /**
     * Find the xterm color index among the 255 xterm colors closest to the given color
     */
    protected function findClosestColor(ColorInterface $color): int
    {
        $found = -1;
        $closest = 1000;

        // iterates over all xterm colors
        foreach (self::xtermRgbColor as $idx => $hex) {
            if (preg_match('#^([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$#', $hex, $splitted)) {
                // calculates distance (with taxicab geometry) between $color and current xterm color
                $distance = abs($color->getRed() - hexdec($splitted[1]))
                    + abs($color->getGreen() - hexdec($splitted[2]))
                    + abs($color->getBlue() - hexdec($splitted[3]));
                if ($distance < $closest) {
                    $closest = $distance;
                    $found = $idx;
                }
            }
        }

        return $found;
    }
}
