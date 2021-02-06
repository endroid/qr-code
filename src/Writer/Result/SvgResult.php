<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

final class SvgResult extends AbstractResult
{
    /** @var \SimpleXMLElement */
    private $xml;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    public function getMimeType(): string
    {
        return 'image/svg+xml';
    }

    public function getXml(): \SimpleXMLElement
    {
        return $this->xml;
    }

    public function getString(): string
    {
        $string = $this->xml->asXML();

        if (!is_string($string)) {
            throw new \Exception('Could not save SVG XML to string');
        }

        return $string;
    }
}
