<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

interface QrCodeInterface
{
    public function getText(): string;

    public function getSize(): int;

    public function getMargin(): int;

    public function getForegroundColor(): array;

    public function getBackgroundColor(): array;

    public function getEncoding(): string;

    public function getRoundBlockSize(): bool;

    public function getErrorCorrectionLevel(): string;

    public function getLogoPath();

    public function getLogoWidth();

    public function getLabel();

    public function getLabelFontPath();

    public function getLabelFontSize();

    public function getLabelAlignment();

    public function getLabelMargin();

    public function getValidateResult(): bool;

    public function getContentType(): string;

    public function setWriterRegistry(WriterRegistryInterface $writerRegistry): void;

    public function writeString(): string;

    public function writeDataUri(): string;

    public function writeFile(string $path): void;
}
