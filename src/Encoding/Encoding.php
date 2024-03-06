<?php

declare(strict_types=1);

namespace Endroid\QrCode\Encoding;

final class Encoding implements EncodingInterface
{
    private const ENCODINGS = [
        'BASE64',
        'UUENCODE',
        'HTML-ENTITIES',
        'Quoted-Printable',
        '7bit',
        '8bit',
        'UCS-4',
        'UCS-4BE',
        'UCS-4LE',
        'UCS-2',
        'UCS-2BE',
        'UCS-2LE',
        'UTF-32',
        'UTF-32BE',
        'UTF-32LE',
        'UTF-16',
        'UTF-16BE',
        'UTF-16LE',
        'UTF-8',
        'UTF-7',
        'UTF7-IMAP',
        'ASCII',
        'EUC-JP',
        'SJIS',
        'eucJP-win',
        'EUC-JP-2004',
        'SJIS-Mobile#DOCOMO',
        'SJIS-Mobile#KDDI',
        'SJIS-Mobile#SOFTBANK',
        'SJIS-mac',
        'SJIS-2004',
        'UTF-8-Mobile#DOCOMO',
        'UTF-8-Mobile#KDDI-A',
        'UTF-8-Mobile#KDDI-B',
        'UTF-8-Mobile#SOFTBANK',
        'CP932',
        'SJIS-win',
        'CP51932',
        'JIS',
        'ISO-2022-JP',
        'ISO-2022-JP-MS',
        'GB18030',
        'Windows-1252',
        'Windows-1254',
        'ISO-8859-1',
        'ISO-8859-2',
        'ISO-8859-3',
        'ISO-8859-4',
        'ISO-8859-5',
        'ISO-8859-6',
        'ISO-8859-7',
        'ISO-8859-8',
        'ISO-8859-9',
        'ISO-8859-10',
        'ISO-8859-13',
        'ISO-8859-14',
        'ISO-8859-15',
        'ISO-8859-16',
        'EUC-CN',
        'CP936',
        'HZ',
        'EUC-TW',
        'BIG-5',
        'CP950',
        'EUC-KR',
        'UHC',
        'ISO-2022-KR',
        'Windows-1251',
        'CP866',
        'KOI8-R',
        'KOI8-U',
        'ArmSCII-8',
        'CP850',
        'ISO-2022-JP-2004',
        'ISO-2022-JP-MOBILE#KDDI',
        'CP50220',
        'CP50221',
        'CP50222',
    ];

    public function __construct(
        private readonly string $value
    ) {
        if (!in_array($value, self::ENCODINGS)) {
            throw new \Exception(sprintf('Invalid encoding "%s": choose one of '.implode(', ', self::ENCODINGS), $value));
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
