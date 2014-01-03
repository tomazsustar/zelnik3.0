<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		1.2.6.2
 * @license		GNU/GPL v2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @copyright	(C) 2008-2013 Kuneri Ltd.
 * @date		July 2013
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class ImageRescaler
{
	static $thumbdir = 'Resized';
	static $addstyles = false;
	static $forced_width = null;
	static $forced_height = null;
	static $scaledimage_width = null;
	static $scaledimage_height = null;
	static $scaletype = 0;
	static $fullwidth = false;
	static $nowrap = false;

	static function RescaleImages($text, $scaletype = 0, $addstyles = false)
	{
		ImageRescaler::$scaletype = $scaletype;
		ImageRescaler::$addstyles = $addstyles;
		return preg_replace_callback('#<img(\s[^>]*?)\s?/?>#i', array('ImageRescaler','imageParsing'), $text);
	}

	static function imageParsing($matches)
	{
		$text = $matches[1];

		ImageRescaler::$forced_width  = 0;
		ImageRescaler::$forced_height = 0;

		ImageRescaler::$fullwidth = false;
		ImageRescaler::$nowrap = false;
		// classes
		if(preg_match('#\sclass\s*=\s*([\'"])(.*?)\1#is', $text, $matches))
		{
			$classes = $matches[2];
			if(preg_match('#\bmj-noscale\b#is', $classes))
				return '<img '.$text.'>';
			if(preg_match('#\bmj-fullwidth\b#is', $classes))
				ImageRescaler::$fullwidth = true;
			if(preg_match('#\bmj-nowrap\b#is', $classes))
				ImageRescaler::$nowrap = true;
		}

		// size
		if(preg_match('#[^\w-]width\s*:\s*(\d+)\s*(px|!|;)#i',  $text, $matches))
			ImageRescaler::$forced_width  = intval($matches[1]);
		elseif(preg_match('#\swidth\s*=\s*([\'"]?)(\d+)\1#i',  $text, $matches))
			ImageRescaler::$forced_width  = intval($matches[2]);

		if(preg_match('#[^\w-]height\s*:\s*(\d+)\s*(px|!|;)#i', $text, $matches))
			ImageRescaler::$forced_height = intval($matches[1]);
		elseif(preg_match('#\sheight\s*=\s*([\'"]?)(\d+)\1#i', $text, $matches))
			ImageRescaler::$forced_height = intval($matches[2]);

		// align
		if(preg_match('#[^\w-]float\s*:\s*(left|right)\s*(!|;)#i', $text, $matches))
			$align = $matches[1];
		elseif(preg_match('#\salign\s*=\s*([\'"]?)(left|right)\1#i', $text, $matches))
			$align = $matches[2];

		// remove parsed data
		$text = preg_replace('#\s(width|height)\s*=\s*([\'"]?)\d*%?\2#i', '', $text);
		$text = preg_replace('#\salign\s*=\s*([\'"]?)(left|right)\1#i', '', $text);
		$text = preg_replace('#\sstyle\s*=\s*([\'"]).*?\1#is', '', $text);

		// rescale
		ImageRescaler::$scaledimage_width  = ImageRescaler::$forced_width;
		ImageRescaler::$scaledimage_height = ImageRescaler::$forced_height;
		$text = preg_replace('#\ssrc\s*=\s*(["\']?)(.*?)\1(?=\s|$)#ise',
							 "' src=\"'.ImageRescaler::rescaleImage('\\2').'\"'", $text);

		if(ImageRescaler::$scaledimage_width && ImageRescaler::$scaledimage_height)
		{
			$text = ' width="' .ImageRescaler::$scaledimage_width .'"'.
					' height="'.ImageRescaler::$scaledimage_height.'"'.
					$text;
			if(ImageRescaler::$addstyles)
				$text .= ' style="width:' .ImageRescaler::$scaledimage_width .'px !important;'.
								 'height:'.ImageRescaler::$scaledimage_height.'px !important;"';
		}

		// check resulting size
		$MobileJoomla_Device =& MobileJoomla::getDevice();
		if(!ImageRescaler::$nowrap && ImageRescaler::$scaledimage_width>$MobileJoomla_Device['screenwidth']/2)
		{
			$text = '<span class="mjwideimg"><img'.$text.' /></span>';
		}
		else
		{
			if(isset($align))
				$text .= ' align="'.$align.'"';
			$text = '<img'.$text.' />';
		}

		return $text;
	}

	static function getmtime($file)
	{
		$time = @filemtime($file);
		if(strtolower(substr(PHP_OS, 0, 3)) !== 'win')
			return $time;
		$fileDST = (date('I', $time) == 1);
		$systemDST = (date('I') == 1);
		if($fileDST==false && $systemDST==true)
			return $time+3600;
		elseif($fileDST==true && $systemDST==false) 
			return $time-3600;
		return $time;
	}

	static function rescaleImage($imageurl)
	{
		$imageurl = str_replace(array('\\"','\\\''), array('"','\''), $imageurl);
		$imageurl = trim($imageurl);

		$src_ext = strtolower(pathinfo($imageurl, PATHINFO_EXTENSION));
		if($src_ext == 'jpeg')
			$src_ext = 'jpg';
		if(!in_array($src_ext, array ('jpg', 'gif', 'png', 'wbmp')))
			return $imageurl;

		$MobileJoomla_Settings =& MobileJoomla::getConfig();
		$base_rel = JURI::base(true).'/';
		$base_abs = JURI::base();
		$imageurl_decoded = urldecode($imageurl);
		if(strpos($imageurl, '//') == false)
		{
			if($imageurl{0}!=='/')
			{
				$src_imagepath = JPATH_SITE.'/'.$imageurl_decoded;
				$imageurl = $base_rel.$imageurl;
			}
			else
			{
				if($base_rel != '/')
				{
					if(strpos($imageurl, $base_rel)!==0)
						return $imageurl;
					$src_imagepath = JPATH_SITE.'/'.substr($imageurl_decoded, strlen($base_rel));
				}
				else
					$src_imagepath = JPATH_SITE.$imageurl_decoded;
			}
		}
		elseif(strpos($imageurl, $base_abs)===0)
			$src_imagepath = JPATH_SITE.'/'.substr($imageurl_decoded, strlen($base_abs));
		elseif($MobileJoomla_Settings['desktop_url'] && strpos($imageurl, $MobileJoomla_Settings['desktop_url'])===0)
			$src_imagepath = JPATH_SITE.'/'.substr($imageurl_decoded, strlen($MobileJoomla_Settings['desktop_url']));
		else
			return $imageurl;

		if(!JFile::exists($src_imagepath))
			return $imageurl;

		list($src_width, $src_height) = getimagesize($src_imagepath);
		if($src_width==0 || $src_height==0)
			return $imageurl;

		$MobileJoomla_Device =& MobileJoomla::getDevice();
		$MobileJoomla        =  MobileJoomla::getInstance();

		$dev_width  = $MobileJoomla_Device['screenwidth'];
		$dev_height = $MobileJoomla_Device['screenheight'];
		$formats    = $MobileJoomla_Device['imageformats'];
		if(!is_array($formats) || count($formats) == 0 || empty($formats[0])) //desktop mode
			return $imageurl;

		if(!ImageRescaler::$fullwidth && $MobileJoomla->getParam('buffer_width') != null)
			$templateBuffer = (int) $MobileJoomla->getParam('buffer_width');
		else
			$templateBuffer = 0;

		$dev_width -= $templateBuffer;
		if($dev_width < 16)
			$dev_width = 16;

		$forced_width  = ImageRescaler::$forced_width;
		$forced_height = ImageRescaler::$forced_height;
		if($forced_width==0)
		{
			if($forced_height==0)
			{
				$forced_width  = $src_width;
				$forced_height = $src_height;
			}
			else
			{
				$forced_width = round($src_width*$forced_height/$src_height);
				if($forced_width==0)
					$forced_width = 1;
			}
		}
		elseif($forced_height==0)
		{
			$forced_height = round($src_height*$forced_width/$src_width);
			if($forced_height==0)
				$forced_height = 1;
		}

		if(ImageRescaler::$scaletype == 1)
		{
			$scalewidth = $MobileJoomla_Settings['templatewidth'];
			$defscale = $dev_width/$scalewidth;
		}
		else
			$defscale = 1;

		$maxscalex = $dev_width/$forced_width;
		$maxscaley = $dev_height/$forced_height;
		$scale = min($defscale, $maxscalex, $maxscaley);
		if($scale >= 1 && in_array($src_ext, $formats) &&
			$forced_width==$src_width && $forced_height==$src_height)
		{
			ImageRescaler::$scaledimage_width  = $src_width;
			ImageRescaler::$scaledimage_height = $src_height;
			return $imageurl;
		}
		$dest_width  = ImageRescaler::$scaledimage_width  = round($forced_width *$scale);
		$dest_height = ImageRescaler::$scaledimage_height = round($forced_height*$scale);
		if($dest_width ==0) $dest_width  = 1;
		if($dest_height==0) $dest_height = 1;

		$jpegquality = $MobileJoomla_Settings['jpegquality'];
		if(isset($MobileJoomla_Settings['hiresimages']) && $MobileJoomla_Settings['hiresimages'] != 0 &&
			isset($MobileJoomla_Device['pixelratio']) && $MobileJoomla_Device['pixelratio'] != 0)
		{
			$dest_width  *= $MobileJoomla_Device['pixelratio'];
			$dest_height *= $MobileJoomla_Device['pixelratio'];
			$jpegquality = $MobileJoomla_Settings['hijpegquality'];
		}

		if(in_array($src_ext, $formats))
			$dest_ext = $src_ext;
		else
			$dest_ext = $formats[0];

		if(defined('PATHINFO_FILENAME'))
			$src_imagename = pathinfo($imageurl_decoded, PATHINFO_FILENAME);
		else
		{
			$base = basename($imageurl_decoded);
			$src_imagename = substr($base, 0, strrpos($base, '.'));
		}

		$dest_imagedir = dirname($src_imagepath).'/'.ImageRescaler::$thumbdir;
		$dest_imagepath = $dest_imagedir.'/'.$src_imagename.'_'.$dest_width.'x'.$dest_height.'.'.$dest_ext;
		$dest_imageuri = $base_rel.implode('/', explode('/', substr($dest_imagepath, strlen(JPATH_SITE.'/'))));
		$dest_imageuri = str_replace(array('%',   ' ',   '"',   '#',   "'",   '+'),
									 array('%25', '%20', '%22', '%23', '%27', '%2B'),
									 $dest_imageuri);

		$src_mtime = ImageRescaler::getmtime($src_imagepath);
		if(JFile::exists($dest_imagepath))
		{
			$dest_mtime = ImageRescaler::getmtime($dest_imagepath);
			if($src_mtime == $dest_mtime)
				return $dest_imageuri;
		}

		if(!JFolder::exists($dest_imagedir))
		{
			JFolder::create($dest_imagedir);
			$indexhtml = '<html><body bgcolor="#FFFFFF"></body></html>';
			JFile::write($dest_imagedir.'/index.html', $indexhtml);
		}

		if(!JFile::copy($src_imagepath, $dest_imagepath))
			return $imageurl;

		switch($src_ext)
		{
			case 'jpg':
				$src_image = @ImageCreateFromJPEG($dest_imagepath);
				break;
			case 'gif':
				$content = file_get_contents($dest_imagepath);
				if(ImageRescaler::is_gif_ani($content))
					return $imageurl;
				$src_image = @ImageCreateFromString($content);
				unset($content);
				break;
			case 'wbmp':
				$src_image = @ImageCreateFromWBMP($dest_imagepath);
				break;
			case 'png':
				$src_image = @ImageCreateFromPNG($dest_imagepath);
				break;
		}
		JFile::delete($dest_imagepath);

		if($src_image==false)
			return $imageurl;

		$dest_image = ImageCreateTrueColor($dest_width, $dest_height);

		//Additional operations to preserve transparency on images
		switch($dest_ext)
		{
		case 'png':
		case 'gif':
			ImageAlphaBlending($dest_image, false);
			$color = ImageColorTransparent($dest_image, ImageColorAllocateAlpha($dest_image, 0, 0, 0, 127));
			ImageFilledRectangle($dest_image, 0, 0, $dest_width, $dest_height, $color);
			ImageSaveAlpha($dest_image, true);
			break;
		default:
			$color = ImageColorAllocate($dest_image, 255, 255, 255);
			ImageFilledRectangle($dest_image, 0, 0, $dest_width, $dest_height, $color);
			break;
		}

		if(function_exists('imagecopyresampled'))
			$ret = ImageCopyResampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
		else
			$ret = ImageCopyResized($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
		if(!$ret)
		{
			ImageDestroy($src_image);
			ImageDestroy($dest_image);
			return $imageurl;
		}
		ImageDestroy($src_image);

		ob_start();
		switch($dest_ext)
		{
			case 'jpg':
				ImageJPEG($dest_image, null, $jpegquality);
				$data = ob_get_contents();
				$data = ImageRescaler::jpeg_clean($data);
				if($data !== false)
				{
					ob_clean();
					echo $data;
				}
				break;
			case 'gif':
				ImageTrueColorToPalette($dest_image, true, 256);
				ImageGIF($dest_image);
				break;
			case 'wbmp':
				// Floyd-Steinberg dithering
				$black = ImageColorAllocate($dest_image, 0,0,0);
				$white = ImageColorAllocate($dest_image, 255,255,255);
				$next_err = array_fill(0, $dest_width, 0);
				for($y=0; $y<$dest_height; $y++)
				{
					$cur_err = $next_err;
					$next_err = array(-1=>0, 0=>0);
					for($x=0, $err=0; $x<$dest_width; $x++)
					{
						$rgb = ImageColorAt($dest_image, $x, $y);
						$r = ($rgb >> 16) & 0xFF;
						$g = ($rgb >> 8) & 0xFF;
						$b = $rgb & 0xFF;
						$color = $err + $cur_err[$x] + 0.299*$r + 0.587*$g + 0.114*$b;
						if($color >= 128)
						{
							ImageSetPixel($dest_image, $x, $y, $white);
							$err = $color-255;
						}
						else
						{
							ImageSetPixel($dest_image, $x, $y, $black);
							$err = $color;
						}
						$next_err[$x-1] += $err*3/16;
						$next_err[$x]   += $err*5/16;
						$next_err[$x+1]  = $err/16;
						$err *= 7/16;
					}
				}
				ImageWBMP($dest_image);
				break;
			case 'png':
				if(version_compare(PHP_VERSION, '5.1.3', '>='))
					ImagePNG($dest_image, null, 9, PNG_ALL_FILTERS);
				elseif(version_compare(PHP_VERSION, '5.1.2', '>='))
					ImagePNG($dest_image, null, 9);
				else
					ImagePNG($dest_image);
				break;
		}
		$data = ob_get_contents();
		ob_end_clean();
		ImageDestroy($dest_image);
		JFile::write($dest_imagepath, $data);
		@touch($dest_imagepath, $src_mtime);

		return $dest_imageuri;
	}

	/* Remove JFIF and Comment headers from GD2-generated jpeg (saves 79 bytes) */
	function jpeg_clean($jpeg_src)
	{
		$jpeg_clr = "\xFF\xD8";
		if(substr($jpeg_src, 0, 2) != $jpeg_clr)
			return false;
		$pos = 2;
		$size = strlen($jpeg_src);
		while($pos < $size)
		{
			if($jpeg_src{$pos} != "\xFF")
				return false;
			$b = $jpeg_src{$pos+1};
			if($b == "\xDA")
				return $jpeg_clr . substr($jpeg_src, $pos);
			$len = array_shift(unpack('n', substr($jpeg_src, $pos + 2, 2)));
			if($b != "\xE0" && $b != "\xFE")
				$jpeg_clr .= substr($jpeg_src, $pos, $len + 2);
			$pos += $len + 2;
		}
		return false;
	}

	/* Count animation frames in gif file, return TRUE if two or more */
	function is_gif_ani($content)
	{
		$count = preg_match_all('#\x00\x21\xF9\x04.{4}\x00(?:\x2C|\x21)#s', $content, $matches);
		return $count > 1;
	}
}
