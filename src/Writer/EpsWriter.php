<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Color\Value\CMYK;
use Color\Value\RGB;
use Endroid\QrCode\QrCodeInterface;

class EpsWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        $data = $qrCode->getData();

        switch ($qrCode->getBackgroundColor())
        {
            case RGB::class:
                $backgroundColor = $qrCode->getBackgroundColor()->getFormattedValue('%d %d %d').' setrgbcolor';
                break;

            case CMYK::class:
                $backgroundColor = $qrCode->getBackgroundColor()->getFormattedValue('%d %d %d %d').' setcmykcolor';
                break;

            default:
                $backgroundColor = '';
                break;
        }
        
        switch ($qrCode->getForegroundColor())
        {
            case RGB::class:
                $foregroundColor = $qrCode->getForegroundColor()->getFormattedValue('%d %d %d').' setrgbcolor';
                break;

            case CMYK::class:
                $foregroundColor = $qrCode->getForegroundColor()->getFormattedValue('%d %d %d %d').' setcmykcolor';
                break;

            default:
                $foregroundColor = '';
                break;
        }
        
        $epsData = [];
        $epsData[] = '%!PS-Adobe-3.0 EPSF-3.0';
        $epsData[] = '%%BoundingBox: 0 0 '.$data['outer_width'].' '.$data['outer_height'];
        $epsData[] = '/F { rectfill } def';
        $epsData[] = $backgroundColor;
        $epsData[] = '0 0 '.$data['outer_width'].' '.$data['outer_height'].' F';
        $epsData[] = $foregroundColor;

        foreach ($data['matrix'] as $row => $values) {
            foreach ($values as $column => $value) {
                if (1 === $value) {
                    $x = $data['margin_left'] + $data['block_size'] * $column;
                    $y = $data['margin_left'] + $data['block_size'] * $row;
                    $epsData[] = $x.' '.$y.' '.$data['block_size'].' '.$data['block_size'].' F';
                }
            }
        }

        return implode("\n", $epsData);
    }

    public static function getContentType(): string
    {
        return 'image/eps';
    }

    public static function getSupportedExtensions(): array
    {
        return ['eps'];
    }

    public function getName(): string
    {
        return 'eps';
    }
}
