<?php

declare(strict_types=1);

namespace Endroid\QrCode;

use BaconQrCode\Common\ErrorCorrectionLevel as BaconErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;

class Matrix
{
    private int $blockCount;
    private int $blockSize;

    public static function fromQrCode(QrCodeInterface $qrCode): self
    {
        $baconErrorCorrectionLevel = self::getBaconErrorCorrectionLevel($qrCode->getErrorCorrectionLevel());
        $baconQrCode = Encoder::encode($qrCode->getData(), $baconErrorCorrectionLevel, $qrCode->getEncoding()->getName());
        $baconMatrix = $baconQrCode->getMatrix();

        dump($baconMatrix);
        die;

        $matrix = [];
        $columnCount = $baconMatrix->getWidth();
        $rowCount = $baconMatrix->getHeight();
        for ($rowIndex = 0; $rowIndex < $rowCount; ++$rowIndex) {
            $matrix[$rowIndex] = [];
            for ($columnIndex = 0; $columnIndex < $columnCount; ++$columnIndex) {
                $matrix[$rowIndex][$columnIndex] = $baconMatrix->get($columnIndex, $rowIndex);
            }
        }

        $data = ['matrix' => $matrix];

        $matrix = new self();
        $matrix->blockCount = count($matrix[0]);
        $matrix->blockSize = $this->size / $data['block_count'];
        if ($this->roundBlockSize) {
            switch ($this->roundBlockSizeMode) {
                case self::ROUND_BLOCK_SIZE_MODE_ENLARGE:
                    $data['block_size'] = intval(ceil($data['block_size']));
                    $this->size = $data['block_size'] * $data['block_count'];
                    break;
                case self::ROUND_BLOCK_SIZE_MODE_SHRINK:
                    $data['block_size'] = intval(floor($data['block_size']));
                    $this->size = $data['block_size'] * $data['block_count'];
                    break;
                case self::ROUND_BLOCK_SIZE_MODE_MARGIN:
                default:
                    $data['block_size'] = intval(floor($data['block_size']));
            }
        }
        $data['inner_width'] = $data['block_size'] * $data['block_count'];
        $data['inner_height'] = $data['block_size'] * $data['block_count'];
        $data['outer_width'] = $this->size + 2 * $this->margin;
        $data['outer_height'] = $this->size + 2 * $this->margin;
        $data['margin_left'] = ($data['outer_width'] - $data['inner_width']) / 2;
        if ($this->roundBlockSize) {
            $data['margin_left'] = intval(floor($data['margin_left']));
        }
        $data['margin_right'] = $data['outer_width'] - $data['inner_width'] - $data['margin_left'];

        return $data;
    }

    private static function getBaconErrorCorrectionLevel(ErrorCorrectionLevelInterface $errorCorrectionLevel): BaconErrorCorrectionLevel
    {
        switch (get_class($errorCorrectionLevel)) {
            case ErrorCorrectionLevel\High::class:
                return BaconErrorCorrectionLevel::valueOf('H');
            case ErrorCorrectionLevel\Low::class:
                return BaconErrorCorrectionLevel::valueOf('L');
            case ErrorCorrectionLevel\Medium::class:
                return BaconErrorCorrectionLevel::valueOf('M');
            case ErrorCorrectionLevel\Quartile::class:
                return BaconErrorCorrectionLevel::valueOf('Q');
        }

        throw new \Exception('Could not convert error correction level to bacon error correction level');
    }
}
