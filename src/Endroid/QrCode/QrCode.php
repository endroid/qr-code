<?php

namespace Endroid\QrCode;

use Endroid\QrCode\Exceptions\DataDoesntExistsException;
use Endroid\QrCode\Exceptions\VersionTooLargeException;
use Endroid\QrCode\Exceptions\ImageSizeTooLargeException;
use Endroid\QrCode\Exceptions\ImageFunctionUnknownException;

/**
 * Generate QR Code
 */
class QrCode {

    /** @const int Error Correction Level Low (7%) */
    const LEVEL_L = 1;

    /** @const int Error Correction Level Medium (15%) */
    const LEVEL_M = 0;

    /** @const int Error Correction Level Quartile (25%) */
    const LEVEL_Q = 3;

    /** @const int Error Correction Level High (30%) */
    const LEVEL_H = 2;

    /** @var string */
    protected $text = '';

    /** @var int */
    protected $size = 0;

    /** @var int */
    protected $padding = 16;

    /** @var resource */
    protected $image = null;

    /** @var int */
    protected $version;

    /** @var int */
    protected $error_correction = self::LEVEL_M;

    /** @var array */
    protected $error_corrections_available = array(self::LEVEL_L, self::LEVEL_M, self::LEVEL_Q, self::LEVEL_H);

    /** @var int */
    protected $module_size;

    /** @var string */
    protected $image_type = 'png';

    /** @var array */
    protected $image_types_available = array('gif', 'png', 'jpeg', 'wbmp');

    /** @var int */
    protected $structure_append_n;

    /** @var int */
    protected $structure_append_m;

    /** @var int */
    protected $structure_append_parity;

    /** @var string */
    protected $structure_append_original_data;

    /**
     * Set structure append
     *
     * @param int $n
     * @param int $m
     * @param int $parity           Parity
     * @param string $original_data Original data
     */
    public function setStructureAppend($n, $m, $parity, $original_data)
    {
        $this->structure_append_n = $n;
        $this->structure_append_m = $m;
        $this->structure_append_parity = $parity;
        $this->structure_append_original_data = $original_data;
    }

    /**
     * Set QR Code version
     *
     * @param int $version QR Code version
     */
    public function setVersion($version)
    {
        if ($version <= 40 && $version >= 0)
        {
            $this->version = $version;
        }
    }

    /**
     * Return QR Code version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set QR Code error correction level
     *
     * @param int $error_correction Error Correction Level
     */
    public function setErrorCorrection($error_correction)
    {
        if (in_array($error_correction, $this->error_corrections_available))
        {
            $this->error_correction = $error_correction;
        }
    }

    /**
     * Return QR Code error correction level
     *
     * @return int
     */
    public function getErrorCorrection()
    {
        return $this->error_correction;
    }

    /**
     * Set QR Code module size
     *
     * @param int $module_size Module size
     */
    public function setModuleSize($module_size)
    {
        $this->module_size = $module_size;
    }

    /**
     * Return QR Code module size
     *
     * @return int
     */
    public function getModuleSize()
    {
        return $this->module_size;
    }

    /**
     * Set image type for rendering
     *
     * @param string $image_type Image type
     */
    public function setImageType($image_type)
    {
        if (in_array($image_type, $this->image_types_available))
        {
            $this->image_type = $image_type;
        }
    }

    /**
     * Return image type for rendering
     *
     * @return string
     */
    public function getImageType()
    {
        return $this->image_type;
    }

    /**
     * Set text to hide in QR Code
     *
     * @param string $text Text to hide
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Return text that will be hid in QR Code
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set QR Code size (width)
     *
     * @param int $size Width of the QR Code
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Return QR Code size (width)
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set padding around the QR Code
     *
     * @param int $padding Padding around QR Code
     */
    public function setPadding($padding)
    {
        $this->padding = $padding;
    }

    /**
     * Return padding around the QR Code
     *
     * @return int
     */
    public function getPadding()
    {
        return $this->padding;
    }

    /**
     * Render the QR Code than save it to given file name
     *
     * @param string $filename File name of the QR Code
     */
    public function save($filename)
    {
        $this->render($filename);
    }

    /**
     * Render the QR Code than save it to given file name or
     * output it to the browser if not file name given.
     *
     * @param null|string $filename File name of the QR Code
     */
    public function render($filename = null)
    {
        $this->create();

        if (is_null($filename))
        {
            imagepng($this->image);
            die;
        }
        else
        {
            imagepng($this->image, $filename);
        }
    }

    /**
     * Create QR Code and return its content
     *
     * @param string|null $format Image type (gif, png, wbmp, jpeg)
     * @return string
     */
    public function get($format = null)
    {
        $this->create();

        if ($format == 'jpg')
        {
            $format = 'jpeg';
        }

        if ( ! in_array($format, $this->image_types_available))
        {
            $format = $this->image_type;
        }

        if ( ! function_exists('image' . $format))
        {
            throw new ImageFunctionUnknownException('QRCode: function image' . $format . ' does not exists.');
        }

        ob_start();
        call_user_func('image' . $format, $this->image);

        return ob_get_clean();
    }

    /**
     * Create the image.
     *
     * @throws Exceptions\DataDoesntExistsException
     * @throws Exceptions\VersionTooLargeException
     * @throws Exceptions\ImageSizeTooLargeException
     * @throws \OverflowException
     */
    public function create()
    {
        $target_filename = null;
        $source_data = $this->text;

		$path = __DIR__ . '/../../../assets/data';
		$image_path = __DIR__ . '/../../../assets/image';

		$version_ul = 40;

		$qrcode_data_string = $source_data;//@$_GET["d"];

		$qrcode_error_correct = $this->error_correction;//@$_GET["e"];
		$qrcode_module_size = $this->module_size;//@$_GET["s"];
		$qrcode_version = $this->version;//@$_GET["v"];
		$qrcode_image_type = $this->image_type;//@$_GET["t"];

		$qrcode_structureappend_n = $this->structure_append_n;//@$_GET["n"];
		$qrcode_structureappend_m = $this->structure_append_m;//@$_GET["m"];
		$qrcode_structureappend_parity = $this->structure_append_parity;//@$_GET["p"];
		$qrcode_structureappend_originaldata = $this->structure_append_original_data;//@$_GET["o"];

		if ($qrcode_module_size <= 0)
        {
		    if ($qrcode_image_type == 'jpeg')
            {
		        $qrcode_module_size = 8;
		    }
            else
            {
		        $qrcode_module_size = 4;
		    }
		}

		$qrcode_data_string = rawurldecode($qrcode_data_string);
		$data_length = strlen($qrcode_data_string);

		if ($data_length <= 0)
        {
            throw new DataDoesntExistsException('QRCode: Data does not exists.');
		    //trigger_error("QRcode : Data do not exist.",E_USER_ERROR);
		    //exit;
		}

		$data_counter = 0;

		if ($qrcode_structureappend_n > 1 && $qrcode_structureappend_n <= 16
		    && $qrcode_structureappend_m > 0 && $qrcode_structureappend_m <= 16)
        {

		    $data_value[0] = 3;
		    $data_bits[0] = 4;

		    $data_value[1] = $qrcode_structureappend_m - 1;
		    $data_bits[1] = 4;

		    $data_value[2] = $qrcode_structureappend_n - 1;
		    $data_bits[2] = 4;


		    $originaldata_length = strlen($qrcode_structureappend_originaldata);

		    if ($originaldata_length > 1)
            {
		        $qrcode_structureappend_parity = 0;
		        $i = 0;

		        while ($i < $originaldata_length)
                {
		            $qrcode_structureappend_parity =
                        ($qrcode_structureappend_parity ^ ord(substr($qrcode_structureappend_originaldata, $i, 1)));
		            $i++;
		        }
		    }

		    $data_value[3] = $qrcode_structureappend_parity;
		    $data_bits[3] = 8;

		    $data_counter = 4;
		}

		$data_bits[$data_counter] = 4;

		/*  --- determine encode mode */
		if (preg_match('/[^\d]/', $qrcode_data_string) != 0)
        {
		    if (preg_match('/[^0-9A-Z \$\*\%\+\.\/\:\-]/', $qrcode_data_string) != 0)
            {
		        /*  --- 8bit byte mode */
		        $codeword_num_plus = array(0,0,0,0,0,0,0,0,0,0,8,8,8,8,
                                           8,8,8,8,8,8,8,8,8,8,8,8,8,8,
                                           8,8,8,8,8,8,8,8,8,8,8,8,8);

		        $data_value[$data_counter] = 4;
		        $data_counter++;
		        $data_value[$data_counter] = $data_length;
		        $data_bits[$data_counter] = 8;   /* #version 1-9 */
		        $codeword_num_counter_value = $data_counter;

		        $data_counter++;
		        $i = 0;

		        while ($i < $data_length)
                {
		            $data_value[$data_counter] = ord(substr($qrcode_data_string, $i, 1));
		            $data_bits[$data_counter] = 8;
		            $data_counter++;
		            $i++;
		        }
		    }
            else
            {
		        /* ---- alphanumeric mode */
		        $codeword_num_plus = array(0,0,0,0,0,0,0,0,0,0,
                                           2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,
                                           4,4,4,4,4,4,4,4,4,4,4,4,4,4);

		        $data_value[$data_counter] = 2;
		        $data_counter++;
		        $data_value[$data_counter] = $data_length;
		        $data_bits[$data_counter] = 9; /* #version 1-9 */
		        $codeword_num_counter_value = $data_counter;

		        $alphanumeric_character_hash = array(
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    'A' => 10,
                    'B' => 11,
                    'C' => 12,
                    'D' => 13,
                    'E' => 14,
                    'F' => 15,
                    'G' => 16,
                    'H' => 17,
                    'I' => 18,
                    'J' => 19,
                    'K' => 20,
                    'L' => 21,
                    'M' => 22,
                    'N' => 23,
                    'O' => 24,
                    'P' => 25,
                    'Q' => 26,
                    'R' => 27,
                    'S' => 28,
                    'T' => 29,
                    'U' => 30,
                    'V' => 31,
                    'W' => 32,
                    'X' => 33,
                    'Y' => 34,
                    'Z' => 35,
                    ' ' => 36,
                    '$' => 37,
                    '%' => 38,
                    '*' => 39,
                    '+' => 40,
                    '-' => 41,
                    '.' => 42,
                    '/' => 43,
                    ':' => 44);

		        $i = 0;
		        $data_counter++;

		        while ($i < $data_length)
                {
		            if ($i % 2 == 0)
                    {
		                $data_value[$data_counter] = $alphanumeric_character_hash[substr($qrcode_data_string, $i, 1)];
		                $data_bits[$data_counter] = 6;
		            }
                    else
                    {
		                $data_value[$data_counter] = $data_value[$data_counter] * 45 +
                            $alphanumeric_character_hash[substr($qrcode_data_string, $i, 1)];
		                $data_bits[$data_counter] = 11;
		                $data_counter++;
		            }

		            $i++;
		        }
		    }
		}
        else
        {
		    /* ---- numeric mode */
		    $codeword_num_plus = array(0,0,0,0,0,0,0,0,0,0,
                                       2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,
                                       4,4,4,4,4,4,4,4,4,4,4,4,4,4);

		    $data_value[$data_counter] = 1;
		    $data_counter++;
		    $data_value[$data_counter] = $data_length;
		    $data_bits[$data_counter] = 10; /* #version 1-9 */
		    $codeword_num_counter_value = $data_counter;

		    $i = 0;
		    $data_counter++;

		    while ($i < $data_length)
            {
		        if ($i % 3 == 0)
                {
		            $data_value[$data_counter] = substr($qrcode_data_string, $i, 1);
		            $data_bits[$data_counter] = 4;
		        }
                else
                {
		             $data_value[$data_counter] = $data_value[$data_counter] * 10 + substr($qrcode_data_string, $i, 1);
                     if ($i % 3 == 1)
                     {
                         $data_bits[$data_counter] = 7;
                     }
                     else
                     {
                         $data_bits[$data_counter] = 10;
                         $data_counter++;
                     }
		        }

		        $i++;
		    }
		}

		if (@$data_bits[$data_counter] > 0)
        {
		    $data_counter++;
		}

		$i = 0;
		$total_data_bits = 0;

		while ($i < $data_counter)
        {
		    $total_data_bits += $data_bits[$i];
		    $i++;
		}

        $ecc_character_hash=array(
            'L'=>'1',
            'l'=>'1',
            'M'=>'0',
            'm'=>'0',
            'Q'=>'3',
            'q'=>'3',
            'H'=>'2',
            'h'=>'2');

        if ( ! is_numeric($qrcode_error_correct))
        {
            $ec = @$ecc_character_hash[$qrcode_error_correct];
        }
        else
        {
            $ec = $qrcode_error_correct;
        }

        if ( ! $ec)
        {
            $ec=0;
        }

        $max_data_bits = 0;

		$max_data_bits_array = array(
            0, 128, 224, 352, 512, 688, 864, 992, 1232, 1456, 1728,
            2032, 2320, 2672, 2920, 3320, 3624, 4056, 4504, 5016, 5352,
            5712, 6256, 6880, 7312, 8000, 8496, 9024, 9544, 10136, 10984,
            11640,12328,13048,13800,14496,15312,15936,16816,17728,18672,

            152, 272, 440, 640, 864, 1088, 1248, 1552, 1856, 2192,
            2592, 2960, 3424, 3688, 4184, 4712, 5176, 5768, 6360, 6888,
            7456, 8048, 8752, 9392, 10208, 10960, 11744, 12248, 13048, 13880,
            14744, 15640, 16568, 17528, 18448, 19472, 20528, 21616, 22496, 23648,

            72, 128, 208, 288, 368, 480, 528, 688, 800, 976,
            1120, 1264, 1440, 1576, 1784, 2024, 2264, 2504, 2728, 3080,
            3248, 3536, 3712, 4112, 4304, 4768, 5024, 5288, 5608, 5960,
            6344, 6760, 7208, 7688, 7888, 8432, 8768, 9136, 9776, 10208,

            104, 176, 272, 384, 496, 608, 704, 880, 1056, 1232,
            1440, 1648, 1952, 2088, 2360, 2600, 2936, 3176, 3560, 3880,
            4096, 4544, 4912, 5312, 5744, 6032, 6464, 6968, 7288, 7880,
            8264, 8920, 9368, 9848, 10288, 10832, 11408, 12016, 12656, 13328
		);

		if ( ! is_numeric($qrcode_version))
        {
		    $qrcode_version = 0;
		}

		if ( ! $qrcode_version)
        {
		    /* #--- auto version select */
		    $i = 1 + 40 * $ec;
		    $j= $i + 39;
		    $qrcode_version = 1;

		    while ($i <= $j)
            {
                if ($max_data_bits_array[$i] >= $total_data_bits + $codeword_num_plus[$qrcode_version])
                {
                    $max_data_bits = $max_data_bits_array[$i];
                    break;
                }
                $i++;
                $qrcode_version++;
		    }
		}
        else
        {
		    $max_data_bits = $max_data_bits_array[$qrcode_version + 40 * $ec];
		}

		if ($qrcode_version > $version_ul)
        {
            throw new VersionTooLargeException('QRCode : version too large');
		    //trigger_error("QRcode : too large version.",E_USER_ERROR);
		}

		$total_data_bits += $codeword_num_plus[$qrcode_version];
		$data_bits[$codeword_num_counter_value] += $codeword_num_plus[$qrcode_version];

		$max_codewords_array=array(
            0, 26, 44, 70, 100, 134, 172, 196, 242,
            292, 346, 404, 466, 532, 581, 655, 733, 815, 901, 991, 1085, 1156,
            1258, 1364, 1474, 1588, 1706, 1828, 1921, 2051, 2185, 2323, 2465,
            2611, 2761, 2876, 3034, 3196, 3362, 3532, 3706);

		$max_codewords = $max_codewords_array[$qrcode_version];
		$max_modules_1side = 17 + ($qrcode_version << 2);

		$matrix_remain_bit = array(
            0,0,7,7,7,7,7,0,0,0,0,
            0,0,0,3,3,3,3,3,3,3,4,
            4,4,4,4,4,4,3,3,3,3,3,
            3,3,0,0,0,0,0,0);

		/* ---- read version ECC data file */
		$byte_num = $matrix_remain_bit[$qrcode_version] + ($max_codewords << 3);
		$filename = $path . '/qrv' . $qrcode_version . '_' . $ec . '.dat';

		$fp1 = fopen ($filename, 'rb');
		    $matx = fread($fp1, $byte_num);
		    $maty = fread($fp1, $byte_num);
		    $masks = fread($fp1, $byte_num);
		    $fi_x = fread($fp1, 15);
		    $fi_y = fread($fp1, 15);
		    $rs_ecc_codewords = ord(fread($fp1, 1));
		    $rso = fread($fp1,128);
		fclose($fp1);

		$matrix_x_array = unpack('C*', $matx);
		$matrix_y_array = unpack('C*', $maty);
		$mask_array = unpack('C*', $masks);

		$rs_block_order = unpack('C*', $rso);

		$format_information_x2 = unpack('C*', $fi_x);
		$format_information_y2 = unpack('C*', $fi_y);

		$format_information_x1 = array(0,1,2,3,4,5,7,8,8,8,8,8,8,8,8);
		$format_information_y1 = array(8,8,8,8,8,8,8,8,7,5,4,3,2,1,0);

		$max_data_codewords = ($max_data_bits >> 3);

		$filename = $path . '/rsc' . $rs_ecc_codewords . '.dat';
		$fp0 = fopen($filename, 'rb');
        $rs_cal_table_array = array();
		$i = 0;

		while ($i < 256)
        {
		    $rs_cal_table_array[$i] = fread($fp0, $rs_ecc_codewords);
		    $i++;
		}
		fclose($fp0);

		/*  --- set terminator */
		if ($total_data_bits <= $max_data_bits - 4){
		    $data_value[$data_counter] = 0;
		    $data_bits[$data_counter] = 4;
		}
        else
        {
		    if ($total_data_bits < $max_data_bits)
            {
			    $data_value[$data_counter] = 0;
		        $data_bits[$data_counter] = $max_data_bits - $total_data_bits;
		    }
            else
            {
		        if ($total_data_bits > $max_data_bits)
                {
                    throw new \OverflowException('QRCode : Overflow error');
                    //trigger_error("QRcode : Overflow error",E_USER_ERROR);
                    //exit;
		        }
		    }
		}

		/* ----divide data by 8bit */
		$i = 0;
		$codewords_counter = 0;
		$codewords[0] = 0;
		$remaining_bits = 8;

		while ($i <= $data_counter)
        {
		    $buffer = @$data_value[$i];
		    $buffer_bits = @$data_bits[$i];

		    $flag = 1;
		    while ($flag)
            {
		        if ($remaining_bits > $buffer_bits){
		            $codewords[$codewords_counter] = (($codewords[$codewords_counter] << $buffer_bits) | $buffer);
		            $remaining_bits -= $buffer_bits;
		            $flag = 0;
		        }
                else
                {
		            $buffer_bits -= $remaining_bits;
		            $codewords[$codewords_counter] = (($codewords[$codewords_counter] << $remaining_bits) |
                        ($buffer >> $buffer_bits));

		            if ($buffer_bits == 0)
                    {
		                $flag = 0;
		            }
                    else
                    {
		                $buffer = ($buffer & ((1 << $buffer_bits) - 1));
		                $flag = 1;
		            }

		            $codewords_counter++;

		            if ($codewords_counter < $max_data_codewords - 1)
                    {
		                $codewords[$codewords_counter] = 0;
		            }

		            $remaining_bits = 8;
		        }
		    }
		    $i++;
		}

		if ($remaining_bits != 8)
        {
		    $codewords[$codewords_counter] = $codewords[$codewords_counter] << $remaining_bits;
		}
        else
        {
		    $codewords_counter--;
		}

		/* ----  set padding character */
		if ($codewords_counter < $max_data_codewords - 1)
        {
		    $flag = 1;

		    while ($codewords_counter < $max_data_codewords - 1)
            {
		        $codewords_counter++;
		        if ($flag == 1)
                {
		            $codewords[$codewords_counter] = 236;
		        }
                else
                {
		            $codewords[$codewords_counter] = 17;
		        }

		        $flag = $flag * -1;
		    }
		}

		/* ---- RS-ECC prepare */
		$i = 0;
		$j = 0;
		$rs_block_number = 0;
		$rs_temp[0] = '';

		while ($i < $max_data_codewords)
        {
		    $rs_temp[$rs_block_number] .= chr($codewords[$i]);
		    $j++;

		    if ($j >= $rs_block_order[$rs_block_number + 1] - $rs_ecc_codewords)
            {
		        $j = 0;
		        $rs_block_number++;
		        $rs_temp[$rs_block_number] = '';
		    }

		    $i++;
		}


		/*
		#
		# RS-ECC main
		#
		*/
		$rs_block_number = 0;
		$rs_block_order_num = count($rs_block_order);

		while ($rs_block_number < $rs_block_order_num)
        {
		    $rs_codewords = $rs_block_order[$rs_block_number + 1];
		    $rs_data_codewords = $rs_codewords - $rs_ecc_codewords;

		    $rstemp = $rs_temp[$rs_block_number] . str_repeat(chr(0), $rs_ecc_codewords);
		    $padding_data = str_repeat(chr(0), $rs_data_codewords);

		    $j = $rs_data_codewords;
		    while($j > 0)
            {
		        $first = ord(substr($rstemp, 0, 1));

		        if ($first)
                {
		            $left_chr = substr($rstemp, 1);
		            $cal = $rs_cal_table_array[$first] . $padding_data;
		            $rstemp = $left_chr ^ $cal;
		        }
                else
                {
		            $rstemp = substr($rstemp, 1);
		        }

		        $j--;
		    }

		    $codewords = array_merge($codewords, unpack('C*', $rstemp));

		    $rs_block_number++;
		}

		/* ---- flash matrix */
        $matrix_content = array();
		$i = 0;
		while ($i < $max_modules_1side)
        {
		    $j = 0;
		    while ($j < $max_modules_1side)
            {
		        $matrix_content[$j][$i] = 0;
		        $j++;
		    }
		    $i++;
		}

		/* --- attach data */
		$i = 0;
		while ($i < $max_codewords)
        {
			$codeword_i = $codewords[$i];
		    $j = 8;
		    while ($j >= 1)
            {
		        $codeword_bits_number = ($i << 3) +  $j;
		        $matrix_content[$matrix_x_array[$codeword_bits_number]][ $matrix_y_array[$codeword_bits_number]] =
                    ((255 * ($codeword_i & 1)) ^ $mask_array[$codeword_bits_number]);
		        $codeword_i = $codeword_i >> 1;
		        $j--;
		    }
		    $i++;
		}

		$matrix_remain = $matrix_remain_bit[$qrcode_version];
		while ($matrix_remain)
        {
		    $remain_bit_temp = $matrix_remain + ($max_codewords << 3);
		    $matrix_content[$matrix_x_array[$remain_bit_temp]][$matrix_y_array[$remain_bit_temp]] =
                (255 ^ $mask_array[$remain_bit_temp]);
		    $matrix_remain--;
		}

		#--- mask select
		$min_demerit_score = 0;
        $hor_master = '';
        $ver_master = '';
        $k = 0;

        while($k < $max_modules_1side)
        {
            $l = 0;
            while ($l < $max_modules_1side)
            {
                $hor_master = $hor_master . chr($matrix_content[$l][$k]);
                $ver_master = $ver_master . chr($matrix_content[$k][$l]);
                $l++;
            }
            $k++;
        }

        $i = 0;
		$all_matrix = $max_modules_1side * $max_modules_1side;
        $mask_number = 0;
		while ($i < 8)
        {
		    $demerit_n1 = 0;
		    $ptn_temp = array();
		    $bit = 1 << $i;
		    $bit_r = (~$bit) & 255;
		    $bit_mask = str_repeat(chr($bit), $all_matrix);
		    $hor = $hor_master & $bit_mask;
		    $ver = $ver_master & $bit_mask;

		    $ver_shift1 = $ver . str_repeat(chr(170), $max_modules_1side);
		    $ver_shift2 = str_repeat(chr(170), $max_modules_1side) . $ver;
		    $ver_shift1_0 = $ver . str_repeat(chr(0), $max_modules_1side);
		    $ver_shift2_0 = str_repeat(chr(0), $max_modules_1side) . $ver;
		    $ver_or = chunk_split(~($ver_shift1 | $ver_shift2), $max_modules_1side, chr(170));
		    $ver_and = chunk_split(~($ver_shift1_0 & $ver_shift2_0), $max_modules_1side, chr(170));

		    $hor = chunk_split(~$hor, $max_modules_1side, chr(170));
		    $ver = chunk_split(~$ver, $max_modules_1side, chr(170));
		    $hor = $hor . chr(170) . $ver;

		    $n1_search = '/' . str_repeat(chr(255), 5) . '+|' . str_repeat(chr($bit_r), 5) . '+/';
		    $n3_search = chr($bit_r) . chr(255) . chr($bit_r) . chr($bit_r) . chr($bit_r) . chr(255) . chr($bit_r);

            $demerit_n3 = substr_count($hor, $n3_search) * 40;
            $demerit_n4 = floor(abs(((100 * (substr_count($ver, chr($bit_r)) / ($byte_num))) - 50) / 5)) * 10;

            $n2_search1 = '/' . chr($bit_r) . chr($bit_r) . '+/';
            $n2_search2 = '/' . chr(255) . chr(255) . '+/';
            $demerit_n2 = 0;
            preg_match_all($n2_search1, $ver_and, $ptn_temp);

            foreach ($ptn_temp[0] as $str_temp)
            {
                $demerit_n2 += (strlen($str_temp) - 1);
            }

            $ptn_temp=array();
            preg_match_all($n2_search2, $ver_or, $ptn_temp);
            foreach ($ptn_temp[0] as $str_temp)
            {
                $demerit_n2 += (strlen($str_temp) - 1);
            }
            $demerit_n2 *= 3;

            $ptn_temp=array();

            preg_match_all($n1_search, $hor, $ptn_temp);
            foreach ($ptn_temp[0] as $str_temp)
            {
                $demerit_n1 += (strlen($str_temp) - 2);
            }

            $demerit_score = $demerit_n1 + $demerit_n2 + $demerit_n3 + $demerit_n4;

            if ($demerit_score <= $min_demerit_score || $i == 0)
            {
                $mask_number = $i;
                $min_demerit_score = $demerit_score;
            }

            $i++;
		}

		$mask_content = 1 << $mask_number;

		# --- format information
		$format_information_value = (($ec << 3) | $mask_number);
		$format_information_array = array(
            '101010000010010', '101000100100101', '101111001111100', '101101101001011',
            '100010111111001', '100000011001110', '100111110010111', '100101010100000',
            '111011111000100', '111001011110011', '111110110101010', '111100010011101',
            '110011000101111', '110001100011000', '110110001000001', '110100101110110',
            '001011010001001', '001001110111110', '001110011100111', '001100111010000',
            '000011101100010', '000001001010101', '000110100001100', '000100000111011',
            '011010101011111', '011000001101000', '011111100110001', '011101000000110',
            '010010010110100', '010000110000011', '010111011011010', '010101111101101');

		$i = 0;
		while ($i < 15)
        {
		    $content = substr($format_information_array[$format_information_value], $i, 1);
		    $matrix_content[$format_information_x1[$i]][$format_information_y1[$i]] = $content * 255;
		    $matrix_content[$format_information_x2[$i + 1]][$format_information_y2[$i + 1]] = $content * 255;
		    $i++;
		}

		$mib = $max_modules_1side + 8;

        if ($this->size == 0)
        {
            $this->size = $mib * $qrcode_module_size;
            if ($this->size > 1480)
            {
                throw new ImageSizeTooLargeException('QRCode : Image size too large');
                //trigger_error("QRcode : Too large image size",E_USER_ERROR);
            }
        }


		$output_image = imagecreate($this->size, $this->size);
        imagecolorallocate($output_image, 255, 255, 255);

		$image_path = $image_path . '/qrv' . $qrcode_version . '.png';

		$base_image = imagecreatefrompng($image_path);

		$col[1] = imagecolorallocate($base_image, 0, 0, 0);
		$col[0] = imagecolorallocate($base_image, 255, 255, 255);

		$i = 4;
		$mxe = 4 + $max_modules_1side;
		$ii = 0;

		while ($i < $mxe)
        {
		    $j = 4;
		    $jj = 0;

		    while ($j < $mxe)
            {
		        if ($matrix_content[$ii][$jj] & $mask_content)
                {
		            imagesetpixel($base_image, $i, $j, $col[1]);
		        }

		        $j++;
		        $jj++;
		    }

		    $i++;
		    $ii++;
		}

		imagecopyresampled($output_image, $base_image, $this->padding,
            $this->padding, 4, 4, $this->size - $this->padding * 2,
            $this->size - $this->padding * 2, $mib - 8, $mib - 8);

        $this->image = $output_image;
	}

}
