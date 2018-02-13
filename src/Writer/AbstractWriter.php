<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Endroid\QrCode\QrCodeInterface;

abstract class AbstractWriter implements WriterInterface
{
    protected function getData(QrCodeInterface $qrCode): array
    {
        $name = strtoupper(substr($qrCode->getErrorCorrectionLevel(), 0, 1));
        $errorCorrectionLevel = constant('BaconQrCode\Common\ErrorCorrectionLevel::'.$name);

        $baconQrCode = Encoder::encode($qrCode->getText(), new ErrorCorrectionLevel($errorCorrectionLevel), $qrCode->getEncoding());

        $matrix = $baconQrCode->getMatrix()->getArray()->toArray();
        foreach ($matrix as &$row) {
            $row = $row->toArray();
        }

        $data = ['matrix' => $matrix];
        $data['block_count'] = count($matrix[0]);
        $data['block_size'] = $qrCode->getSize() / $data['block_count'];
        if ($qrCode->getRoundBlockSize()) {
            $data['block_size'] = intval(floor($data['block_size']));
        }
        $data['inner_width'] = $data['block_size'] * $data['block_count'];
        $data['inner_height'] = $data['block_size'] * $data['block_count'];
        $data['outer_width'] = $qrCode->getSize() + 2 * $qrCode->getMargin();
        $data['outer_height'] = $qrCode->getSize() + 2 * $qrCode->getMargin();
        $data['margin_left'] = ($data['outer_width'] - $data['inner_width']) / 2;
        if ($qrCode->getRoundBlockSize()) {
            $data['margin_left'] = intval(floor($data['margin_left']));
        }
        $data['margin_right'] = $data['outer_width'] - $data['inner_width'] - $data['margin_left'];

        return $data;
    }

    public function writeDataUri(QrCodeInterface $qrCode): string
    {
        $dataUri = 'data:'.$this->getContentType().';base64,'.base64_encode($this->writeString($qrCode));

        return $dataUri;
    }

    public function writeFile(QrCodeInterface $qrCode, string $path): void
    {
        $string = $this->writeString($qrCode);
        file_put_contents($path, $string);
    }

    public static function supportsExtension(string $extension): bool
    {
        return in_array($extension, static::getSupportedExtensions());
    }

    public static function getSupportedExtensions(): array
    {
        return [];
    }

    abstract public function getName(): string;
}
