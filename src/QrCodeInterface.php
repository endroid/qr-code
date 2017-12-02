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
    function getText(): string;
    function getSize(): int;
    function getMargin(): int;
    function getForegroundColor(): array;
    function getBackgroundColor(): array;
    function getEncoding(): string;
    function getErrorCorrectionLevel(): string;
    function getLogoPath(): ?string;
    function getLogoWidth(): ?int;
    function getLabel(): ?string;
    function getLabelFontPath(): ?string;
    function getLabelFontSize(): ?int;
    function getLabelAlignment(): ?string;
    function getLabelMargin(): ?array;
    function getValidateResult(): bool;
    function setWriterRegistry(WriterRegistryInterface $writerRegistry): void;
    function writeString(): string;
    function writeDataUri(): string;
    function writeFile(string $path): string;
}
