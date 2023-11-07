<?php

declare(strict_types=1);

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\ImageData\LogoImageData;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\Result\SvgResult;

final class SvgWriterCompact implements WriterInterface
{
    public const DECIMAL_PRECISION = 10;
    public const WRITER_OPTION_BLOCK_ID = 'block_id';
    public const WRITER_OPTION_EXCLUDE_XML_DECLARATION = 'exclude_xml_declaration';
    public const WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT = 'exclude_svg_width_and_height';
    public const WRITER_OPTION_FORCE_XLINK_HREF = 'force_xlink_href';

    public function write(QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, array $options = []): ResultInterface
    {
        if (!isset($options[self::WRITER_OPTION_BLOCK_ID])) {
            $options[self::WRITER_OPTION_BLOCK_ID] = 'block';
        }

        if (!isset($options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION])) {
            $options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION] = false;
        }

        if (!isset($options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT])) {
            $options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT] = false;
        }

        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);





		//echo("<pre>" . print_r($matrix, true) . "</pre>");

		/*===== I) =====
		group adjacent blocks to reduce path drawing instructions later on
		*/
		$bcount = $matrix->getBlockCount();
		$bsize = $matrix->getBlockSize();

		//absolute offsets for first block relative to top left corner of the canvas (0/0)
		$xoff = $yoff = $matrix->getMarginLeft();

		//array to collect line Arrays
		$rows = [];

		//traverse matrix and collect block groups line by line
		for($rowIndex = 0; $rowIndex < $bcount; $rowIndex++){

			//local line array to collect block groups for one rowIndex
			$linea = [];

			//running variable for grouping
			$g = null;

			//blocks in this line
			for($columnIndex = 0; $columnIndex < $bcount; $columnIndex++){

				//get block value 0|1
				$v = $matrix->getBlockValue($rowIndex, $columnIndex);

				if($v === 1){
					// [1] : this block has to be drawn

					//If there is no running group "open" - create a new one
					if(!$g){
						//The actual block is the first block in the group and determines
						//the matrix x/y position for this group of adjacent blocks
						$g = new SvgWriterCompact_Blockgroup($columnIndex, $rowIndex);
					}

					//always count the actual block for the running group (1-n)
					$g->bcount++;
				}
				else{
					// [0] : this block has not to be drawn (empty, gap)
					//It does not belong to the running group

					//close the running group and calc its rectangular shape
					if($g){
						//calculate drawing reactangle immediately to use it in the following steps directly
						$g->calc($xoff, $yoff, $bsize);

						//collect the group for the actual line
						$linea[] = $g;
					}

					//reset running variable!
					//With this a new group will be opened as soon
					//as the next nonempty block [1] appears in the following for columnIndex loops
					$g = null;
				}
			}

			//after the last block for this line has been processed there might be
			//a group remaining "open". This happens if the last block in this line
			//had a block value of [1].
			if($g){
				//finish this group
				$g->calc($xoff, $yoff, $bsize);

				//collect it for this line
				$linea[] = $g;
			}

			//finally collect all groups for this line
			$rows[] = $linea;
		}
		//echo("<pre>" . print_r($rows, true) . "</pre>");



		/*===== II) =====
		We could create <rect> Elements for each block group. But this will also produce
		a lot of SVG markup and does not solve the problem of flashing lines between adjacent blocks.
		The proper solution is to combine all drawing statements into one <path> Element.
		This removes flashing edges in all browsers and as well in all tested graphics software (inkscape, illustrator, affinity, libre office, ...).
		The reason might be that the resulting combined path is handled as only one path and not a group of pathes.

		Further reading for SVG at: https://www.mediaevent.de/tutorial/svg-path.html
		*/
		//array for collecting single rectangular drawing statements M...Z
		$da = [];
		foreach($rows as $linea){
			foreach($linea as $p){
				//draw one closed rectangular shape
				//It's allowed to chain multiple line segment statements without separating whitespace.
				//We move the cursor to the start position "M", draw linear path segments
				//with "L" clockwise and close the rectangular shape with "Z".
				$da[] = "M{$p->x1},{$p->y1}L{$p->x2},{$p->y1}L{$p->x2},{$p->y2}L{$p->x1},{$p->y2}Z";
			}
		}
		//Get the combined String. Now the draw statements of all block groups for all rows are combined in one single
		//statement that can be set as the @d Attribute of the <path> Element.
		$path_d = implode(" ", $da);



		//===== III) create SVG =====
		//root element
		$xml = new \SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
		$xml->addAttribute('version', '1.1');
		if (!$options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT]) {
			$xml->addAttribute('width', $matrix->getOuterSize().'px');
			$xml->addAttribute('height', $matrix->getOuterSize().'px');
		}
		$xml->addAttribute('viewBox', '0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize());

		//background rectangle
		$background = $xml->addChild('rect');
		$background->addAttribute('x', '0');
		$background->addAttribute('y', '0');
		$background->addAttribute('width', strval($matrix->getOuterSize()));
		$background->addAttribute('height', strval($matrix->getOuterSize()));
		$background->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()->getRed(), $qrCode->getBackgroundColor()->getGreen(), $qrCode->getBackgroundColor()->getBlue()));
		$background->addAttribute('fill-opacity', strval($qrCode->getBackgroundColor()->getOpacity()));

		//Symbol as one combined <path> for all visible blocks [1]
		$pelm = $xml->addChild("path");
		//fill color
		$fc = '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()->getRed(), $qrCode->getForegroundColor()->getGreen(), $qrCode->getForegroundColor()->getBlue());
		//fill opacity
		$fo = strval($qrCode->getForegroundColor()->getOpacity());
		$pelm->addAttribute("fill", $fc);
		$pelm->addAttribute("fill-opacity", $fo);
		//The drawing statement
		$pelm->addAttribute("d", $path_d);



/*
//old method
        $xml = new \SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
        $xml->addAttribute('version', '1.1');
        if (!$options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT]) {
            $xml->addAttribute('width', $matrix->getOuterSize().'px');
            $xml->addAttribute('height', $matrix->getOuterSize().'px');
        }
        $xml->addAttribute('viewBox', '0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize());
        $xml->addChild('defs');

        $blockDefinition = $xml->defs->addChild('rect');
        $blockDefinition->addAttribute('id', strval($options[self::WRITER_OPTION_BLOCK_ID]));
        $blockDefinition->addAttribute('width', number_format($matrix->getBlockSize(), self::DECIMAL_PRECISION, '.', ''));
        $blockDefinition->addAttribute('height', number_format($matrix->getBlockSize(), self::DECIMAL_PRECISION, '.', ''));
        $blockDefinition->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()->getRed(), $qrCode->getForegroundColor()->getGreen(), $qrCode->getForegroundColor()->getBlue()));
        $blockDefinition->addAttribute('fill-opacity', strval($qrCode->getForegroundColor()->getOpacity()));

        $background = $xml->addChild('rect');
        $background->addAttribute('x', '0');
        $background->addAttribute('y', '0');
        $background->addAttribute('width', strval($matrix->getOuterSize()));
        $background->addAttribute('height', strval($matrix->getOuterSize()));
        $background->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()->getRed(), $qrCode->getBackgroundColor()->getGreen(), $qrCode->getBackgroundColor()->getBlue()));
        $background->addAttribute('fill-opacity', strval($qrCode->getBackgroundColor()->getOpacity()));

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    $block = $xml->addChild('use');
                    $block->addAttribute('x', number_format($matrix->getMarginLeft() + $matrix->getBlockSize() * $columnIndex, self::DECIMAL_PRECISION, '.', ''));
                    $block->addAttribute('y', number_format($matrix->getMarginLeft() + $matrix->getBlockSize() * $rowIndex, self::DECIMAL_PRECISION, '.', ''));
                    $block->addAttribute('xlink:href', '#'.$options[self::WRITER_OPTION_BLOCK_ID], 'http://www.w3.org/1999/xlink');
                }
            }
        }

//insert <path> Element as overlay to check
//if it fits all blocks drawn by the old method with <use> Elements.
$pelm = $xml->addChild("path");
$pelm->addAttribute("fill", "red");
$pelm->addAttribute("fill-opacity", "0.5");
$pelm->addAttribute("d", $path_d);

*/

        $result = new SvgResult($matrix, $xml, boolval($options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION]));

        if ($logo instanceof LogoInterface) {
            $this->addLogo($logo, $result, $options);
        }

        return $result;
    }

    /** @param array<string, mixed> $options */
    private function addLogo(LogoInterface $logo, SvgResult $result, array $options): void
    {
        $logoImageData = LogoImageData::createForLogo($logo);

        if (!isset($options[self::WRITER_OPTION_FORCE_XLINK_HREF])) {
            $options[self::WRITER_OPTION_FORCE_XLINK_HREF] = false;
        }

        $xml = $result->getXml();

        /** @var \SimpleXMLElement $xmlAttributes */
        $xmlAttributes = $xml->attributes();

        $x = intval($xmlAttributes->width) / 2 - $logoImageData->getWidth() / 2;
        $y = intval($xmlAttributes->height) / 2 - $logoImageData->getHeight() / 2;

        $imageDefinition = $xml->addChild('image');
        $imageDefinition->addAttribute('x', strval($x));
        $imageDefinition->addAttribute('y', strval($y));
        $imageDefinition->addAttribute('width', strval($logoImageData->getWidth()));
        $imageDefinition->addAttribute('height', strval($logoImageData->getHeight()));
        $imageDefinition->addAttribute('preserveAspectRatio', 'none');

        if ($options[self::WRITER_OPTION_FORCE_XLINK_HREF]) {
            $imageDefinition->addAttribute('xlink:href', $logoImageData->createDataUri(), 'http://www.w3.org/1999/xlink');
        } else {
            $imageDefinition->addAttribute('href', $logoImageData->createDataUri());
        }
    }
}



//Helper class for grouping horizontaly adjacent blocks with values of [1]
class SvgWriterCompact_Blockgroup{
	//start index in the matrix, 0-n
	public $ix = 0;
	public $iy = 0;

	//number of blocks in this group, 1-n
	public $bcount = 0;

	//absolute coordinates and size after calc()
	public $x1 = 0;
	public $y1 = 0;
	public $w = 0;
	public $h = 0;
	public $x2 = 0;
	public $y2 = 0;


	/*calculate drawing rectangle in absolute pixel coordinates

	The rectangle may be a square if the group contains only one block.
	Or a more or less wide rectangle depending on the number of blocks in the group.
	This drawing rectangle can be used to render a SVG <rect> element
	or a drawing statement for a SVG <path> element @d Attribute.

	ATTENTION: i assume that for SVG the default setRoundBlockSizeMode is the
	only reasonable mode and therefore all values are INT.

	For resolution independend vector graphics this might be irrelevant.
	But float numbers produce a large amount of redundant overhead in the coordinate values.
	Especially SVG output like "M 10.0000000000,20.0000000000" is completely useless.
	It should better be "M 10,20".

	The resulting SVG vectorgraphic file only consists of rectangular shapes with rounded
	values and can be positioned/edited in any vector graphics software and scaled to any size
	always at highest quality.

	There is no reason for highest precision in the point values here.
	*/
	function calc($xoff=0, $yoff=0, $block_size=0){
		//top left corner
		$this->x1 = $xoff + ($this->ix * $block_size);
		$this->y1 = $yoff + ($this->iy * $block_size);

		//width/height
		$this->w = $this->bcount * $block_size;
		$this->h = $block_size;

		//bottom right corner
		$this->x2 = $this->x1 + $this->w;
		$this->y2 = $this->y1 + $this->h;
	}

	function __construct($ix=0, $iy=0){
		$this->ix = $ix;
		$this->iy = $iy;
	}
}
