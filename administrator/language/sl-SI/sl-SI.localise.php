<?php
/**
 * @version		$Id: language.php 15628 2010-03-27 05:20:29Z infograf768 $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * en-GB localise class
 *
 * @package		Joomla.Site
 * @since		1.6
 */
abstract class uk_UALocalise {
	/**
	 * Returns the potential suffixes for a specific number of items
	 *
	 * @param	int $count  The number of items.
	 * @return	array  An array of potential suffixes.
	 * @since	1.6
	 */
	public static function getPluralSuffixes($count)
	{
		if ($count == 0) {
			$return = array('0');
		} else {
			$return = array(($count%10==1 && $count%100!=11 ? '1' : ($count%10>=2 && $count%10<=4 && ($count%100<10 || $count%100>=20) ? '2' : 'MORE')));
		}
		return $return;
	}

	public static function transliterate($string)
	{
        switch(uk_UALocalise::simple_detect_language($string))
        {
            case 'si':
        		$str = JString::strtolower($string);

        		$glyph_array = array();
		$search_ignore[] = "in";
		$search_ignore[] = "v";
		$search_ignore[] = "na";
		return $search_ignore;
	}
	/**
	 * Returns the lower length limit of search words
	 *
	 * @return	integer  The lower length limit of search words.
	 * @since	1.6
	 */
	public static function getLowerLimitSearchWord() {
		return 3;
	}
	/**
	 * Returns the upper length limit of search words
	 *
	 * @return	integer  The upper length limit of search words.
	 * @since	1.6
	 */
	public static function getUpperLimitSearchWord() {
		return 30;
	}
	/**
	 * Returns the number of chars to display when searching
	 *
	 * @return	integer  The number of chars to display when searching.
	 * @since	1.6
	 */
	public static function getSearchDisplayedCharactersNumber() {
		return 300;
	}
}

