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
    private $data = [];

    protected function getData(QrCodeInterface $qrCode): array
    {
        if (0 !== count($this->data)) {
            return $this->data;
        }

        $name = strtoupper(substr($qrCode->getErrorCorrectionLevel(), 0, 1));
        $errorCorrectionLevel = constant('BaconQrCode\Common\ErrorCorrectionLevel::'.$name);

        $baconQrCode = Encoder::encode($qrCode->getText(), new ErrorCorrectionLevel($errorCorrectionLevel), $qrCode->getEncoding());

        $matrix = $baconQrCode->getMatrix()->getArray()->toArray();
        foreach ($matrix as &$row) {
            $row = $row->toArray();
        }

        $this->data['matrix'] = $matrix;
        $this->data['block_count'] = count($matrix[0]);
        $this->data['block_size'] = $qrCode->getSize() / $this->data['block_count'];
        if ($qrCode->getRoundBlockSize()) {
            $this->data['block_size'] = intval(floor($this->data['block_size']));
        }
        $this->data['inner_width'] = $this->data['block_size'] * $this->data['block_count'];
        $this->data['inner_height'] = $this->data['block_size'] * $this->data['block_count'];
        $this->data['outer_width'] = $qrCode->getSize() + 2 * $qrCode->getMargin();
        $this->data['outer_height'] = $qrCode->getSize() + 2 * $qrCode->getMargin();
        $this->data['margin_left'] = ($this->data['outer_width'] - $this->data['inner_width']) / 2;
        if ($qrCode->getRoundBlockSize()) {
            $this->data['margin_left'] = intval(floor($this->data['margin_left']));
        }
        $this->data['margin_right'] = $this->data['outer_width'] - $this->data['inner_width'] - $this->data['margin_left'];

        return $this->data;
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
