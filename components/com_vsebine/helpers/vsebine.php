<?php
/**
 * @version     1.0.0
 * @package     com_vsebine
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Tomaž Šuštar <tomaz@zelnik.net> - http://www.zelnik.net
 */

defined('_JEXEC') or die;

abstract class VsebineHelper
{
	public static function myFunction()
	{
		$result = 'Something';
		return $result;
	}
	
	public static  function wraplines($input, $line_max = 76, $quotedprintable = false)
	{
		$hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
		$eol = "\r\n";

		$input = str_replace($eol, "", $input);

		// new version

		$output = '';
		while (JString::strlen($input) >= $line_max)
		{
			$output .= JString::substr($input, 0, $line_max - 1);
			$input = JString::substr($input, $line_max - 1);
			if (strlen($input) > 0)
			{
				$output .= $eol . " ";
			}
		}
		if (strlen($input) > 0)
		{
			$output .= $input;
		}
		return $output;

		$escape = '=';
		$output = '';
		$outline = "";
		$newline = ' ';

		$linlen = JString::strlen($input);


		for ($i = 0; $i < $linlen; $i++)
		{
			$c = JString::substr($input, $i, 1);

			/*
			  $dec = ord($c);
			  if (!$quotedprintable) {
			  if (($dec == 32) && ($i == ($linlen - 1))) { // convert space at eol only
			  $c = '=20';
			  } elseif (($dec == 61) || ($dec < 32 ) || ($dec > 126)) { // always encode "\t", which is *not* required
			  $h2 = floor($dec / 16);
			  $h1 = floor($dec % 16);
			  $c = $escape . $hex["$h2"] . $hex["$h1"];
			  }
			  }
			 */
			if ((strlen($outline) + 1) >= $line_max)
			{ // CRLF is not counted
				$output .= $outline . $eol . $newline; // soft line break; "\r\n" is okay
				$outline = $c;
				//$newline .= " ";
			}
			else
			{
				$outline .= $c;
			}
		} // end of for
		$output .= $outline;
		return trim($output);
	}

}

