<?php

declare(strict_types=1);

namespace Endroid\QrCode\Tests;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\BinaryWriter;
use Endroid\QrCode\Writer\ConsoleWriter;
use Endroid\QrCode\Writer\DebugWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\GifWriter;
use Endroid\QrCode\Writer\PdfWriter;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\BinaryResult;
use Endroid\QrCode\Writer\Result\ConsoleResult;
use Endroid\QrCode\Writer\Result\DebugResult;
use Endroid\QrCode\Writer\Result\EpsResult;
use Endroid\QrCode\Writer\Result\GifResult;
use Endroid\QrCode\Writer\Result\PdfResult;
use Endroid\QrCode\Writer\Result\PngResult;
use Endroid\QrCode\Writer\Result\SvgResult;
use Endroid\QrCode\Writer\Result\WebpResult;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\ValidatingWriterInterface;
use Endroid\QrCode\Writer\WebPWriter;
use Endroid\QrCode\Writer\WriterInterface;

use Endroid\QrCode\Writer\SvgWriterCompact;


spl_autoload_register(function($class_name){
	$cpath = str_replace("Endroid\\QrCode\\", "", $class_name);

	$fpath = "../src/" . $cpath . ".php";
/*
echo("
<pre>
class_name: $class_name
cpath: $cpath
fpath: $fpath
</pre>
");
*/
	if(@file_exists($fpath))require_once $fpath;
});

class C4dTest{
	public static function testSvgWriterCompact(int $writerSwitch=0){
		$dataString = "https://www.4d-image.de/";

		$size = 1024;
		$margin = 62;
		$size_use = $size - ($margin * 2);

		$qrCode = QrCode::create($dataString)
			->setEncoding(new Encoding('UTF-8'))
			->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize($size_use)
            ->setMargin($margin)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

		if($writerSwitch === 1){
			//use new SVGWriterCompact
			$writer = new SvgWriterCompact();
		}
		else{
			//use default SvgWriter
			$writer = new SvgWriter();
		}

		$result = $writer->write($qrCode);

		// Directly output the QR code
		header('Content-Type: '.$result->getMimeType());
		echo $result->getString();
	}
}

//Respond with QR-Code Data instead of HTML test document.
$mode = filter_input(
	INPUT_GET,
	"mode"
);
if(!$mode)$mode = "";


//switch between writers
$writer = filter_input(
	INPUT_GET,
	"writer",
	FILTER_VALIDATE_INT,
	[
		"options" => [
			"default" => 0,
			"min_range" => 0,
			"max_range" => 1
		]
	]
);

if($mode === "data"){
	C4dTest::testSvgWriterCompact($writer);
	die();
}

//source URLs of the <img> elements in the <body>
$img_src = "?mode=data&writer=0";
$img_src_1 = "?mode=data&writer=1";

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>qr-code SvgWriterCompact demo</title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<style>
html, html *{
	box-sizing:border-box;
}
html{
	font-family:sans-serif;
	font-size:16px;
}

img{
	max-width:100%;
	height:auto;
}
.codes{
	display:flex;
	flex-wrap:wrap;
	align-items:start;
}
</style>
</head>
<body>

<!--
<pre>
<?php
echo("
mode: $mode
writer: $writer
img_src: $img_src
");
?>
</pre>
-->

<h2>SvgWriter (default)</h2>
<ol>
<li>Causes thin flashing edges between adjacent blocks when not displayed at intrinsic size</li>
<li>Large file size simply because of redundant SVG markup</li>
</ol>
<div class="codes">
<img src="<?=$img_src?>" width="512" height="512" alt="QR-Code">
<img src="<?=$img_src?>" width="256" height="256" alt="QR-Code">
<img src="<?=$img_src?>" width="128" height="128" alt="QR-Code">
</div>
<hr>

<h2>SvgWriterCompact</h2>
<ol>
<li>No flashing edges no matter what size</li>
<li>Largely reduced file size</li>
</ol>
<div class="codes">
<img src="<?=$img_src_1?>" width="512" height="512" alt="QR-Code">
<img src="<?=$img_src_1?>" width="256" height="256" alt="QR-Code">
<img src="<?=$img_src_1?>" width="128" height="128" alt="QR-Code">
</div>

</body>
</html>