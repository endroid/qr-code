<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer\Result;

final class SvgResult extends AbstractResult
{
    private \SimpleXMLElement $xml;
    private bool $excludeXmlDeclaration;

    public function __construct(\SimpleXMLElement $xml, bool $excludeXmlDeclaration = false)
    {
        $this->xml = $xml;
        $this->excludeXmlDeclaration = $excludeXmlDeclaration;
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

        if ($this->excludeXmlDeclaration) {
            $string = str_replace("<?xml version=\"1.0\"?>\n", '', $string);
        }

        return $string;
    }

    public function getMimeType(): string
    {
        return 'image/svg+xml';
    }
}
