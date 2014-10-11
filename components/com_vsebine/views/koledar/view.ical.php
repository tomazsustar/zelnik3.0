<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

include_once JPATH_SITE.'/components/com_vsebine/helpers/vsebine.php';
//include_once JPATH_SITE.'/components/com_vsebine/helpers/html2text.php';
/**
 * HTML View class for the Content component
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.5
 */
class VsebineViewKoledar extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app       = JFactory::getApplication();
		//$doc       = JFactory::getDocument();
		//$params    = $app->getParams();
		//$feedEmail = $app->getCfg('feed_email', 'author');
		//$siteEmail = $app->getCfg('mailfrom');

		// Get some data from the model
		$standard = $app->input->get('standard', 0);
		$full = $app->input->get('full', 0);
		//$category = $this->get('Category');
		$dnevi     = $this->get('Items');
		//$doc->link = JRoute::_(ContentHelperRoute::getCategoryRoute($category->id));
		
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . 'calendar.ics');
		 
		// 2. Define helper functions
		 
		// Converts a unix timestamp to an ics-friendly format
		// NOTE: "Z" means that this timestamp is a UTC timestamp. If you need
		// to set a locale, remove the "\Z" and modify DTEND, DTSTAMP and DTSTART
		// with TZID properties (see RFC 5545 section 3.3.5 for info)
//		function dateToCal($timestamp) {
//		  return date('Ymd\Tgis\Z', $timestamp);
//		}
		 
		// Escapes a string of characters
//		function escapeString($string) {
//		  return preg_replace('/([\,;])/','\\\$1', $string);
//		}
		 
		// 3. Echo out the ics file's contents
//		

//	
//		END:VCALENDAR';
		
?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//zelnik.net/novicomat//EN
CALSCALE:GREGORIAN
<?php 
foreach ($dnevi as $dogodki):
foreach ($dogodki as $dogodek):
?>
BEGIN:VEVENT
SUMMARY:<?= preg_replace('/([\,;])/','\\\$1', $dogodek->naslov) ?>

ATTACH:<?= $dogodek->slika ?>

UID:<?= $dogodek->koledar_id.'-koledar@novicomat.si' ?>

DTSTAMP:<?= gmdate('Ymd\THis\Z', time()) ?>

LOCATION:<?= preg_replace('/([\,;])/','\\\$1', $dogodek->lokacija) ?>

<?php if(!$standard):?>
DESCRIPTION;ENCODING=QUOTED-PRINTABLE:<?= VsebineHelper::wraplines('<img src="'.$dogodek->slika.'" alt="slika" style="float:left;margin:5px;" />'.$dogodek->fulltext) // preg_replace('/([\,;])/','\\\$1', strip_tags($dogodek->fulltext)), 76, true  ?>
<?php else:?>
<?php if($full):?>
DESCRIPTION:<?= preg_replace('/([\,;])/','\\\$1', strip_tags($dogodek->fulltext)) ?>
<?php else:?>
DESCRIPTION:<?= preg_replace('/([\,;])/','\\\$1', strip_tags($dogodek->introtext)). ' - ' . "http://".$_SERVER['HTTP_HOST'].$dogodek->url ?>
<?php endif;?>
<?php endif;?>

URL;VALUE=URI:<?= "http://".$_SERVER['HTTP_HOST'].$dogodek->url ?>

<?php if($dogodek->zacetek->format('His')=="000000"):?>
DTSTART;VALUE=DATE:<?= $dogodek->zacetek->format('Ymd') ?>
<?php else:?>
<?php $dogodek->zacetek->setTimezone(new DateTimeZone('UTC')); ?>
DTSTART:<?= $dogodek->zacetek->format('Ymd\THis\Z') ?>
<?php endif;?>

<?php if(!$dogodek->konec):?>
DTEND;VALUE=DATE:<?= $dogodek->zacetek->modify("+1 day")->format('Ymd') ?>
<?php elseif ($dogodek->konec->format('His')=="000000"):?>
DTEND;VALUE=DATE:<?= $dogodek->konec->modify("+1 day")->format('Ymd') ?>
<?php else:?>
<?php $dogodek->konec->setTimezone(new DateTimeZone('UTC')); ?>
DTEND:<?= $dogodek->konec->format('Ymd\THis\Z') ?>
<?php endif;?>

END:VEVENT
<?php endforeach;
endforeach;?>
END:VCALENDAR
<?php 
	}
}
