<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;
use SimpleXMLElement;

class SvgWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {

        $data = $this->getData($qrCode);

        $svg = new SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
        $svg->addAttribute('version', '1.1');
        $svg->addAttribute('width', $data['inner_width'].'px');
        $svg->addAttribute('height', $data['inner_height'].'px');
        $svg->addAttribute('viewBox', '0 0 '.$data['outer_width'].' '.$data['outer_height']);
        $svg->addChild('defs');
        
        		
        // Block definition
        $blockDefinition = $svg->defs->addChild('rect');
        $blockDefinition->addAttribute('id', 'block');
        $blockDefinition->addAttribute('width', $data['block_size']);
        $blockDefinition->addAttribute('height', $data['block_size']);
        $blockDefinition->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()['r'], $qrCode->getForegroundColor()['g'], $qrCode->getForegroundColor()['b']));
        $blockDefinition->addAttribute('fill-opacity', $this->getOpacity($qrCode->getForegroundColor()['a']));

        // Background
        $background = $svg->addChild('rect');
        $background->addAttribute('x', 0);
        $background->addAttribute('y', 0);
        $background->addAttribute('width', $data['outer_width']);
        $background->addAttribute('height', $data['outer_height']);
        $background->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b']));
        $background->addAttribute('fill-opacity', $this->getOpacity($qrCode->getBackgroundColor()['a']));

        foreach ($data['matrix'] as $row => $values) {
            foreach ($values as $column => $value) {
                if (1 === $value) {
                    $block = $svg->addChild('use');
                    $block->addAttribute('x', $data['margin_left'] + $data['block_size'] * $column);
                    $block->addAttribute('y', $data['margin_left'] + $data['block_size'] * $row);
                    $block->addAttribute('xlink:href', '#block', 'http://www.w3.org/1999/xlink');
                }
            }
        }
        
		//image
	    if ($qrCode->getLogoPath()) {
		    
		    #this results in: $image->image_data $image->image_height and $image->imagewidth
            $image = $this->resizeAndGetLogo($qrCode->getLogoPath(), $qrCode->getLogoWidth());
            
            # create new block
	        $blockDefinition = $svg->addChild('image');
	        $blockDefinition->addAttribute('width', $image->image_width);
	        $blockDefinition->addAttribute('height', $image->image_height);
	        
	        $blockDefinition->addAttribute('x', (($data['outer_width']-$data['margin_right'])/2) - (($image->image_width/2)-10));
	        $blockDefinition->addAttribute('y', (($data['outer_height']-$data['margin_right'])/2) - (($image->image_height/2)-10));
	        
	        $blockDefinition->addAttribute('xlink:href', $image->image_data);
        }
        
        $xml = $svg->asXML();

        $options = $qrCode->getWriterOptions();
        if (isset($options['exclude_xml_declaration']) && $options['exclude_xml_declaration']) {
            $xml = str_replace("<?xml version=\"1.0\"?>\n", '', $xml);
        }
        return $xml;
    }

    private function getOpacity(int $alpha): float
    {
        $opacity = 1 - $alpha / 127;

        return $opacity;
    }

    public static function getContentType(): string
    {
        return 'image/svg+xml';
    }

    public static function getSupportedExtensions(): array
    {
        return ['svg'];
    }

    public function getName(): string
    {
        return 'svg';
    }
    
	private function resizeAndGetLogo(string $logoPath, int $logoWidth = null)
    {
	    
	    if (!$this->isSvg($logoPath)) {
		    
		    $logoWidth = $logoWidth*2;
		    
	        $logoImage = imagecreatefromstring(file_get_contents($logoPath));
	
	        $logoSourceWidth = imagesx($logoImage);
	        $logoSourceHeight = imagesy($logoImage);
	        
			$ratio = $logoSourceWidth/$logoSourceHeight;		
			
		    $width = $logoWidth/$ratio;
		    $height = $logoWidth;
		    
			$dst = imagecreatetruecolor($width,$height);
			
			//handle PNG transparancy
			imagealphablending($dst, false);
			imagesavealpha($dst,true);
			$transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
			imagefilledrectangle($dst, 0, 0, $width, $height, $transparent);
			
			//create the image
			imagecopyresampled($dst,$logoImage,0,0,0,0,$width,$height,$logoSourceWidth,$logoSourceHeight);
			
			//buffer the image and output as PNG file
			ob_start();
			imagepng($dst);
			// Capture the output
			$imagedata = ob_get_contents();
			// Clear the output buffer
			ob_end_clean();	
			
			$image_data = "data:image/png;base64,".base64_encode($imagedata);        
        } else {
	        $image_data = "data:image/svg+xml;base64,".base64_encode(file_get_contents($logoPath));
	        $width = $logoWidth*2;
	        $height = $logoWidth*2;
        }
        
        return (object)[
	        "image_data" => $image_data,
	        "image_width" => $width/2,
	        "image_height" => $height/2
        ];

    }
    
	public function isSvg($filePath)
	{
	    return (mime_content_type($filePath) === 'image/svg+xml');
	}        
}
